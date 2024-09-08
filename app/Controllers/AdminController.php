<?php

require_once __DIR__ . '/../core/Database.php';

class AdminController extends Controller
{
    private $db;
    private $configFile = __DIR__ . '/../config/style.json';

    public function __construct()
    {
        if (!isset($_SESSION['admin_logged_in'])) {
            redirect('/admin/login');
            exit;
        }
        $this->db = Database::getInstance();
    }

    public function index()
    {
        $models = $this->getModels();
        $this->view('admin/index', ['logout' => 'logout', 'configuration' => 'configuration', 'models' => $models]);
    }

    public function list($model)
    {
        $modelClass = ucfirst($model);
        require_once __DIR__ . '/../models/' . $modelClass . '.php';
        $modelInstance = new $modelClass();

        $data = $modelInstance->all();
        $this->view('admin/list', ['data' => $data, 'model' => $model]);
    }

    public function createForm($model)
    {
        $columns = $this->getModelColumns($model);
        $this->view('admin/create', ['modelName' => $model, 'columns' => $columns, 'model' => $model]);
    }

    public function create($model)
    {
        $modelClass = ucfirst($model);
        require_once __DIR__ . '/../models/' . $modelClass . '.php';
        $modelInstance = new $modelClass();

        $result = $modelInstance->create($_POST);
        if (is_array($result)) {
            $columns = $this->getModelColumns($model);
            $modelName = $model;
            $this->view('admin/create', ['modelName' => $model, 'columns' => $columns, 'model' => $model, 'errors' => $result]);
        } else {
            redirect('/admin/models/' . $model);
        }
    }

    public function editForm($model, $id)
    {
        $modelClass = ucfirst($model);
        require_once __DIR__ . '/../models/' . $modelClass . '.php';
        $modelInstance = new $modelClass();

        $data = $modelInstance->find($id);
        $columns = $this->getModelColumns($model);
        $this->view('admin/edit', ['modelName' => $model, 'columns' => $columns, 'colVal' => $data, 'id' => $id]);
    }

    public function edit($model, $id)
    {
        $modelClass = ucfirst($model);
        require_once __DIR__ . '/../models/' . $modelClass . '.php';
        $modelInstance = new $modelClass();

        $result = $modelInstance->update($id, $_POST);
        if (is_array($result)) {
            $data = $modelInstance->find($id);
            $columns = $this->getModelColumns($model);
            $errors = $result;
            $this->view('admin/edit', ['modelName' => $model, 'columns' => $columns, 'colVal' => $data, 'id' => $id, 'errors' => $result]);
        } else {
            redirect('/admin/models/' . $model);
        }
    }

    public function delete($model, $id)
    {
        $modelClass = ucfirst($model);
        require_once __DIR__ . '/../models/' . $modelClass . '.php';
        $modelInstance = new $modelClass();

        $modelInstance->delete($id);
        redirect('/admin/models/' . $model);
    }

    private function getModelColumns($model)
    {
        $modelClass = ucfirst($model);
        require_once __DIR__ . '/../models/' . $modelClass . '.php';
        $reflection = new ReflectionClass($modelClass);
        $properties = $reflection->getProperties();

        $columns = [];
        foreach ($properties as $property) {
            $docComment = $property->getDocComment();
            if ($docComment) {
                preg_match('/@column\((.*?)\)/', $docComment, $columnMatches);
                preg_match('/@type\((.*?)\)/', $docComment, $typeMatches);
                preg_match('/@formType\((.*?)\)/', $docComment, $formTypeMatches);
                preg_match('/@not_editable/', $docComment, $notEditableMatches);

                $column = $columnMatches[1] ?? null;
                $type = $formTypeMatches[1] ?? 'text';
                $isEditable =  !isset($notEditableMatches[0]);

                if ($column && $isEditable) {
                    $columns[$column] = $type;
                }
            }
        }

        return $columns;
    }

    private function getModels()
    {
        $models = [];
        $modelFiles = glob(__DIR__ . '/../models/*.php');
        foreach ($modelFiles as $modelFile) {
            $models[] = basename($modelFile, '.php');
        }
        return $models;
    }

    // Display the configuration form
    public function configurationPanel()
    {
        $data = $this->getCurrentStyles();

        $this->view('admin/configuration', $data);
    }

    // Handle form submission and update styles
    public function configureStyle()
    {
        $configDir = dirname($this->configFile);

        // Check if the config directory exists, if not, create it
        if (!is_dir($configDir)) {
            mkdir($configDir, 0777, true);
        }

        $data = $this->getCurrentStyles();

        // Update styles dynamically based on POST data
        foreach ($data as $key => $value) {
            if (isset($_POST[$key])) {
                $data[$key] = $_POST[$key];
            }
        }

        file_put_contents($this->configFile, json_encode($data, JSON_PRETTY_PRINT));

        $this->view('admin/configuration', $data);
    }

    // Retrieve current styles from config file
    private function getCurrentStyles()
    {
        if (!file_exists($this->configFile)) {
            return [
                'color_primary' => '#007BFF',
                'color_secondary' => '#6C757D',
                'color_success' => '#28A745',
                'color_danger' => '#DC3545',
                'color_warning' => '#FFC107',
                'color_info' => '#17A2B8',
                'color_light' => '#F8F9FA',
                'color_dark' => '#343A40',
                'color_white' => '#FFFFFF',
                'color_black' => '#000000',

                'font_size_small' => '0.8rem',
                'font_size_base' => '1rem',
                'font_size_large' => '1.2rem',
                'font_size_xl' => '1.5rem',

                'font_family_sans' => "'Helvetica Neue', Arial, sans-serif",
                'font_family_serif' => "'Times New Roman', serif",
                'font_family_mono' => "'Courier New', monospace",

                'spacing_xs' => '0.25rem',
                'spacing_sm' => '0.5rem',
                'spacing_md' => '1rem',
                'spacing_lg' => '1.5rem',
                'spacing_xl' => '2rem',

                'border_radius_sm' => '0.2rem',
                'border_radius_md' => '0.5rem',
                'border_radius_lg' => '1rem',

                'shadow_sm' => '0px 1px 3px rgba(0, 0, 0, 0.1)',
                'shadow_md' => '0px 4px 6px rgba(0, 0, 0, 0.1)',
                'shadow_lg' => '0px 10px 20px rgba(0, 0, 0, 0.2)'
            ];
        }

        return json_decode(file_get_contents($this->configFile), true);
    }
}

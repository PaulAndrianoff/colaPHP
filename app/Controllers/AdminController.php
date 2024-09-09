<?php

namespace App\Controllers;

use ReflectionClass;
use App\Core\Database;
use App\Core\Controller;

class AdminController extends Controller
{
    private $db;
    private $configFile;

    public function __construct()
    {
        if (!isset($_SESSION['admin_logged_in'])) {
            redirect('/admin/login');
            exit;
        }
        $this->db = Database::getInstance();
        $this->configFile = __DIR__ . '/../config/style.json';
    }

    public function index()
    {
        $models = $this->getModels();
        $this->view('admin/index', ['models' => $models]);
    }

    public function list($model)
    {
        $modelInstance = $this->loadModel($model);
        $data = $modelInstance->all();
        $this->view('admin/list', ['data' => $data, 'model' => $model]);
    }

    public function createForm($model)
    {
        $columns = $this->getModelColumns($model);
        $this->view('admin/create', ['modelName' => $model, 'columns' => $columns]);
    }

    public function create($model)
    {
        $modelInstance = $this->loadModel($model);

        $result = $modelInstance->create($_POST);
        if (is_array($result)) {
            $columns = $this->getModelColumns($model);
            $this->view('admin/create', ['modelName' => $model, 'columns' => $columns, 'errors' => $result]);
        } else {
            redirect("/admin/models/$model");
        }
    }

    public function editForm($model, $id)
    {
        $modelInstance = $this->loadModel($model);

        $data = $modelInstance->find($id);
        $columns = $this->getModelColumns($model);
        $this->view('admin/edit', ['modelName' => $model, 'columns' => $columns, 'colVal' => $data, 'id' => $id]);
    }

    public function edit($model, $id)
    {
        $modelInstance = $this->loadModel($model);

        $result = $modelInstance->update($id, $_POST);
        if (is_array($result)) {
            $data = $modelInstance->find($id);
            $columns = $this->getModelColumns($model);
            $this->view('admin/edit', ['modelName' => $model, 'columns' => $columns, 'colVal' => $data, 'id' => $id, 'errors' => $result]);
        } else {
            redirect("/admin/models/$model");
        }
    }

    public function delete($model, $id)
    {
        $modelInstance = $this->loadModel($model);
        $modelInstance->delete($id);
        redirect("/admin/models/$model");
    }

    // Handle Configuration Panel
    public function configurationPanel()
    {
        $data = $this->getCurrentStyles();
        $this->view('admin/configuration', $data);
    }

    public function configureStyle()
    {
        $configDir = dirname($this->configFile);

        if (!is_dir($configDir)) {
            mkdir($configDir, 0777, true);
        }

        $data = $this->getCurrentStyles();
        foreach ($data as $key => $value) {
            if (isset($_POST[$key])) {
                $data[$key] = $_POST[$key];
            }
        }

        file_put_contents($this->configFile, json_encode($data, JSON_PRETTY_PRINT));
        $this->view('admin/configuration', $data);
    }

    // Model utility methods
    private function loadModel($model)
    {
        $modelClass = $this->getModelClassName($model);

        if (!class_exists($modelClass)) {
            throw new \Exception("Model class $modelClass not found.");
        }

        return new $modelClass;
    }

    private function getModelClassName($model)
    {
        return 'App\\Models\\' . ucfirst($model);
    }

    private function getModelColumns($model)
    {
        $modelClass = $this->getModelClassName($model);
        $reflection = new ReflectionClass($modelClass);
        $properties = $reflection->getProperties();

        $columns = [];
        foreach ($properties as $property) {
            $docComment = $property->getDocComment();
            if ($docComment) {
                preg_match('/@column\((.*?)\)/', $docComment, $columnMatches);
                preg_match('/@formType\((.*?)\)/', $docComment, $formTypeMatches);
                preg_match('/@not_editable/', $docComment, $notEditableMatches);

                $column = $columnMatches[1] ?? null;
                $type = $formTypeMatches[1] ?? 'text';
                $isEditable = !isset($notEditableMatches[0]);

                if ($column && $isEditable) {
                    $columns[$column] = $type;
                }
            }
        }

        return $columns;
    }

    // Retrieve all models in the models directory
    private function getModels()
    {
        $modelFiles = glob(__DIR__ . '/../Models/*.php');
        return array_map(fn($file) => basename($file, '.php'), $modelFiles);
    }

    // Style configuration methods
    private function getCurrentStyles()
    {
        if (!file_exists($this->configFile)) {
            return $this->getDefaultStyles();
        }

        return json_decode(file_get_contents($this->configFile), true);
    }

    private function getDefaultStyles()
    {
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
            'shadow_lg' => '0px 10px 20px rgba(0, 0, 0, 0.2)',
        ];
    }
}

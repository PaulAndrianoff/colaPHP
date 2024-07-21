<?php

require_once __DIR__ . '/../core/Database.php';

class AdminController extends Controller {
    private $db;

    public function __construct() {
        if (!isset($_SESSION['admin_logged_in'])) {
            redirect('/admin/login');
            exit;
        }
        $this->db = Database::getInstance();
    }

    public function index() {
        $models = $this->getModels();
        $this->view('admin/index', ['logout' => 'logout', 'models' => $models]);
    }

    public function list($model) {
        $modelClass = ucfirst($model);
        require_once __DIR__ . '/../models/' . $modelClass . '.php';
        $modelInstance = new $modelClass();

        $data = $modelInstance->all();
        $this->view('admin/list', ['data' => $data, 'model' => $model]);
    }

    public function createForm($model) {
        $columns = $this->getModelColumns($model);
        $this->view('admin/create', ['columns' => $columns, 'model' => $model]);
    }

    public function create($model) {
        $modelClass = ucfirst($model);
        require_once __DIR__ . '/../models/' . $modelClass . '.php';
        $modelInstance = new $modelClass();

        $modelInstance->create($_POST);
        redirect('/admin/models/' . $model);
    }

    public function editForm($model, $id) {
        $modelClass = ucfirst($model);
        require_once __DIR__ . '/../models/' . $modelClass . '.php';
        $modelInstance = new $modelClass();

        $data = $modelInstance->find($id);
        $columns = $this->getModelColumns($model);
        $this->view('admin/edit', ['columns' => $columns, 'colVal' => $data, 'id' => $id]);
    }

    public function edit($model, $id) {
        $modelClass = ucfirst($model);
        require_once __DIR__ . '/../models/' . $modelClass . '.php';
        $modelInstance = new $modelClass();

        $modelInstance->update($id, $_POST);
        redirect('/admin/models/' . $model);
    }

    public function delete($model, $id) {
        $modelClass = ucfirst($model);
        require_once __DIR__ . '/../models/' . $modelClass . '.php';
        $modelInstance = new $modelClass();

        $modelInstance->delete($id);
        redirect('/admin/models/' . $model);
    }

    private function getModelColumns($model) {
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

    private function getModels() {
        $models = [];
        $modelFiles = glob(__DIR__ . '/../models/*.php');
        foreach ($modelFiles as $modelFile) {
            $models[] = basename($modelFile, '.php');
        }
        return $models;
    }
}

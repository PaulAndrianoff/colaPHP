<?php

require_once __DIR__ . '/../core/Database.php';

class DbPopulateCommand {
    private $arguments;
    private $db;

    public function __construct($arguments) {
        $this->arguments = $arguments;
        $this->db = Database::getInstance(); // Initialize the database connection
    }

    public function execute() {
        if (count($this->arguments) < 3) {
            echo "Usage: " . $this->getSyntax() . "\n";
            return;
        }

        $model = ucfirst($this->arguments[1]);
        $count = (int)$this->arguments[2];
        $modelFile = __DIR__ . '/../Models/' . $model . '.php';

        if (!file_exists($modelFile)) {
            echo "Model does not exist: $model\n";
            return;
        }

        require_once $modelFile;
        $reflection = new ReflectionClass($model);
        $properties = $reflection->getProperties();

        $columns = [];
        $tableName = null;

        foreach ($properties as $property) {
            $docComment = $property->getDocComment();
            if ($docComment) {
                if (preg_match('/@table\((.*?)\)/', $docComment, $tableMatches)) {
                    $tableName = $tableMatches[1];
                }
                preg_match('/@column\((.*?)\)/', $docComment, $columnMatches);
                preg_match('/@type\((.*?)\)/', $docComment, $typeMatches);

                $column = $columnMatches[1] ?? null;
                $type = $typeMatches[1] ?? 'varchar';

                if ($column) {
                    $columns[$column] = $type;
                }
            }
        }

        if (!$tableName) {
            echo "Table name not defined in model: $model\n";
            return;
        }

        if (empty($columns)) {
            echo "No columns defined in model: $model\n";
            return;
        }

        for ($i = 0; $i < $count; $i++) {
            $values = [];
            foreach ($columns as $column => $type) {
                if ('id' !== $column) {
                    $values[$column] = $this->generateFakeData($type, $column);
                }
            }

            $this->insertData($tableName, $values);
        }

        echo "Inserted $count rows into $tableName\n";
    }

    private function generateFakeData($type, $column = '') {
        switch ($type) {
            case 'int':
                return rand(1, 1000);
            case 'varchar':
            case 'text':
                return "'" . $column . "_" . $this->generateRandomString(10) . "'";
            case 'timestamp':
                return "'" . date('Y-m-d H:i:s') . "'";
            default:
                return "'" . $column . "_" . $this->generateRandomString(10) . "'";
        }
    }

    private function generateRandomString($length) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    private function insertData($tableName, $values) {
        $columns = implode(',', array_keys($values));
        $values = implode(',', array_values($values));
        $sql = "INSERT INTO $tableName ($columns) VALUES ($values)";
        $this->db->query($sql);
    }

    public function getSyntax() {
        return "php cli.php db:populate <model> <count>";
    }
}

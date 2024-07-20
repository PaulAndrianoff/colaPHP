<?php

require_once __DIR__ . '/../core/Database.php';

class DbPrepareCommand {
    private $arguments;
    private $db;

    public function __construct($arguments) {
        $this->arguments = $arguments;
        $this->db = Database::getInstance(); // Initialize the database connection
    }

    public function execute() {
        if (count($this->arguments) < 2) {
            echo "Usage: " . $this->getSyntax() . "\n";
            return;
        }

        $model = ucfirst($this->arguments[1]);
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
                preg_match('/@key\((.*?)\)/', $docComment, $keyMatches);
                preg_match('/@length\((.*?)\)/', $docComment, $lengthMatches);
                preg_match('/@not_null/', $docComment, $notNullMatches);

                $column = $columnMatches[1] ?? null;
                $type = $typeMatches[1] ?? 'varchar';
                $key = $keyMatches[1] ?? null;
                $length = $lengthMatches[1] ?? null;
                $notNull = isset($notNullMatches[0]);

                if ($column) {
                    $columns[$column] = [
                        'type' => $type,
                        'key' => $key,
                        'length' => $length,
                        'not_null' => $notNull
                    ];
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

        $existingColumns = $this->getExistingColumns($this->db, $tableName);

        if ($existingColumns === null) {
            $sqlUp = $this->generateCreateTableSQL($tableName, $columns);
            $sqlDown = "DROP TABLE $tableName;";
        } else {
            $sqlUp = $this->generateAlterTableSQL($tableName, $columns, $existingColumns, false);
            $sqlDown = $this->generateAlterTableSQL($tableName, $columns, $existingColumns, true);
        }

        $migrationDir = __DIR__ . '/../../migrations';
        if (!file_exists($migrationDir)) {
            mkdir($migrationDir, 0777, true);
        }

        $currentDate = date('YmdHis');
        $migrationName = 'update_' . $currentDate . '_' . strtolower($model) . '_table.php';
        $migrationFile = $migrationDir . '/' . $migrationName;

        $content = "<?php\n\n";
        $content .= "class Update" . $currentDate . ucfirst($model) . "Table" . " {\n";
        $content .= "    private \$db;\n\n";
        $content .= "    public function __construct(\$db) {\n";
        $content .= "        \$this->db = \$db;\n";
        $content .= "    }\n\n";
        $content .= "    public function up() {\n";
        $content .= "        \$sql = \"$sqlUp\";\n";
        $content .= "        \$this->db->query(\$sql);\n";
        $content .= "    }\n\n";
        $content .= "    public function down() {\n";
        $content .= "        \$sql = \"$sqlDown\";\n";
        $content .= "        \$this->db->query(\$sql);\n";
        $content .= "    }\n";
        $content .= "}\n";

        file_put_contents($migrationFile, $content);
        echo "Migration created: $migrationName\n";
    }

    private function getExistingColumns($db, $tableName) {
        try {
            $result = $db->query("SHOW COLUMNS FROM $tableName");
            if (!$result) {
                return null;
            }

            $columns = [];
            foreach ($result as $row) {
                $columns[$row['Field']] = [
                    'type' => $row['Type'],
                    'key' => $row['Key'],
                    'null' => $row['Null'] === 'NO'
                ];
            }

            return $columns;
        } catch (\Throwable $th) {
            return null;
        }
    }

    private function generateCreateTableSQL($tableName, $columns) {
        $sql = "CREATE TABLE $tableName (\n";
        foreach ($columns as $name => $column) {
            $sql .= "    $name {$column['type']}";
            if ($column['length']) {
                $sql .= "({$column['length']})";
            }
            if ($column['not_null']) {
                $sql .= " NOT NULL";
            }
            if ($column['key']) {
                $keys = explode(';', $column['key']);
                foreach ($keys as $key) {
                    if ($key === 'primary') {
                        $sql .= " PRIMARY KEY";
                    } elseif ($key === 'auto_incr') {
                        $sql .= " AUTO_INCREMENT";
                    }
                }
            }
            $sql .= ",\n";
        }
        $sql = rtrim($sql, ",\n") . "\n);";

        return $sql;
    }

    private function generateAlterTableSQL($tableName, $columns, $existingColumns, $down = false) {
        $sql = "ALTER TABLE $tableName \n";

        $modifications = [];
        $additions = [];
        $deletions = [];

        foreach ($columns as $name => $column) {
            if (isset($existingColumns[$name])) {
                unset($existingColumns[$name]);
            } else {
                if ($down) {
                    $deletions[] = "DROP COLUMN $name";
                } else {
                    $additions[] = $this->generateAddColumnSQL($name, $column);
                }
            }
        }

        foreach ($existingColumns as $name => $column) {
            if (!$down) {
                $deletions[] = "DROP COLUMN $name";
            } else {
                $additions[] = $this->generateAddColumnSQL($name, $column);
            }
        }

        $sql .= implode(",\n", array_merge($modifications, $additions, $deletions)) . ";";

        return $sql;
    }

    private function isColumnModified($newColumn, $existingColumn) {    
        // Normalize the key values
        $newKey = $newColumn['key'] === 'primary;auto_incr' ? 'PRI' : $newColumn['key'];
        $existingKey = $existingColumn['key'];
    
        // Normalize the null values
        $newNull = $newColumn['not_null'] ? 'NO' : 'YES';
        $existingNull = $existingColumn['null'] ? 'NO' : 'YES';

        return $newColumn['type'] !== $existingColumn['type'] ||
               $newKey !== $existingKey ||
               $newNull !== $existingNull;
    }
    

    private function generateModifyColumnSQL($name, $newColumn, $existingColumn) {
        $sql = "MODIFY COLUMN $name {$newColumn['type']}";
        if ($newColumn['length']) {
            $sql .= "({$newColumn['length']})";
        }
        if ($newColumn['not_null']) {
            $sql .= " NOT NULL";
        }
        if ($newColumn['key']) {
            $keys = explode(';', $newColumn['key']);
            foreach ($keys as $key) {
                if ($key === 'primary') {
                    $sql .= " PRIMARY KEY";
                } elseif ($key === 'auto_incr') {
                    $sql .= " AUTO_INCREMENT";
                }
            }
        }
        return $sql;
    }

    private function generateAddColumnSQL($name, $column) {
        $sql = "ADD COLUMN $name {$column['type']}";
        if (isset($column['length'])) {
            $sql .= "({$column['length']})";
        }
        if (isset($column['not_null'])) {
            $sql .= " NOT NULL";
        }
        if ($column['key']) {
            $keys = explode(';', $column['key']);
            foreach ($keys as $key) {
                if ($key === 'primary') {
                    $sql .= " PRIMARY KEY";
                } elseif ($key === 'auto_incr') {
                    $sql .= " AUTO_INCREMENT";
                }
            }
        }
        return $sql;
    }

    public function getSyntax() {
        return "php cli.php db:prepare <model>";
    }
}

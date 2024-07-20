<?php

class DbMigrateCommand {
    private $arguments;

    public function __construct($arguments) {
        $this->arguments = $arguments;
    }

    public function execute() {
        if (count($this->arguments) < 2) {
            echo "Usage: php cli.php db:migrate <model>\n";
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

        foreach ($properties as $property) {
            $docComment = $property->getDocComment();
            if ($docComment) {
                preg_match('/@table\((.*?)\)/', $docComment, $tableMatches);
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

                if ('table' === $property->name) {
                    $tableName = $tableMatches[1];
                }

                if ($column) {
                    $columns[] = [
                        'column' => $column,
                        'type' => $type,
                        'key' => $key,
                        'length' => $length,
                        'not_null' => $notNull
                    ];
                }
            }
        }

        if (empty($columns)) {
            echo "No columns defined in model: $model\n";
            return;
        }

        $migrationFile = __DIR__ . '/../../migrations/' . date('YmdHis') . '_create_' . strtolower($model) . '_table.php';
        // $tableName = strtolower($model);

        $content = "<?php\n\n";
        $content .= "class Create" . ucfirst($model) . "Table {\n";
        $content .= "    private \$db;\n\n";
        $content .= "    public function __construct(\$db) {\n";
        $content .= "        \$this->db = \$db;\n";
        $content .= "    }\n\n";
        $content .= "    public function up() {\n";
        $content .= "        \$sql = \"CREATE TABLE $tableName (\n";

        foreach ($columns as $column) {
            $content .= "            {$column['column']} {$column['type']}";
            if ($column['length']) {
                $content .= "({$column['length']})";
            }
            if ($column['not_null']) {
                $content .= " NOT NULL";
            }
            if ($column['key']) {
                $keys = explode(';', $column['key']);
                foreach ($keys as $key) {
                    if ($key === 'primary') {
                        $content .= " PRIMARY KEY";
                    } elseif ($key === 'auto_incr') {
                        $content .= " AUTO_INCREMENT";
                    }
                }
            }
            $content .= ",\n";
        }

        $content = rtrim($content, ",\n") . "\n        )\";\n";
        $content .= "        \$this->db->query(\$sql);\n";
        $content .= "    }\n\n";
        $content .= "    public function down() {\n";
        $content .= "        \$sql = \"DROP TABLE IF EXISTS $tableName\";\n";
        $content .= "        \$this->db->query(\$sql);\n";
        $content .= "    }\n";
        $content .= "}\n";

        file_put_contents($migrationFile, $content);
        echo "Migration created: $migrationFile\n";
    }

    public function getSyntax() {
        return "php cli.php db:migrate <model>";
    }
}

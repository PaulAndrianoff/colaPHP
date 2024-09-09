<?php

namespace App\core;

use ReflectionClass;

class Command {
    private $arguments;

    public function __construct($arguments) {
        $this->arguments = $arguments;
    }

    public function run() {
        if (count($this->arguments) < 2) {
            echo "Usage: php cli.php [command] [parameters]\n";
            return;
        }

        $action = $this->arguments[1];

        switch ($action) {
            case 'create:route':
                $this->createRoute();
                break;
            case 'migrate':
                $this->migrate();
                break;
            case 'rollback':
                $this->rollback();
                break;
            case 'db:migrate':
                $this->createMigrationFromModel();
                break;
            default:
                echo "Unknown command: $action\n";
        }
    }

    private function createRoute() {
        if (count($this->arguments) < 5) {
            echo "Usage: php cli.php create:route <route> <controller> <view>\n";
            return;
        }

        $route = $this->arguments[2];
        $controller = $this->arguments[3];
        $view = $this->arguments[4];

        $this->createController($controller);
        $this->createView($view);
        $this->addRoute($route, $controller);
    }

    private function createController($controller) {
        $controllerFile = __DIR__ . '/../Controllers/' . $controller . '.php';

        if (file_exists($controllerFile)) {
            echo "Controller already exists: $controller\n";
            return;
        }

        $controllerName = ucfirst($controller);
        $content = "<?php\n\n";
        $content .= "class $controllerName extends Controller {\n";
        $content .= "    public function index() {\n";
        $content .= "        \$this->view('$controller');\n";
        $content .= "    }\n";
        $content .= "}\n";

        file_put_contents($controllerFile, $content);
        echo "Controller created: $controllerFile\n";
    }

    private function createView($view) {
        $viewFile = __DIR__ . '/../Views/' . $view . '.php';

        if (file_exists($viewFile)) {
            echo "View already exists: $view\n";
            return;
        }

        $content = "<!DOCTYPE html>\n<html lang=\"en\">\n<head>\n";
        $content .= "    <meta charset=\"UTF-8\">\n";
        $content .= "    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n";
        $content .= "    <title>$view</title>\n";
        $content .= "</head>\n<body>\n";
        $content .= "    <h1>$view</h1>\n";
        $content .= "</body>\n</html>";

        file_put_contents($viewFile, $content);
        echo "View created: $viewFile\n";
    }

    private function addRoute($route, $controller) {
        $controllerClass = ucfirst($controller);
        $indexFile = __DIR__ . '/../../index.php';

        $routeDefinition = "\$router->get('$route', '$controllerClass@index');\n";

        $indexContent = file_get_contents($indexFile);
        $indexContent = str_replace("\$router->run();", "$routeDefinition\$router->run();", $indexContent);

        file_put_contents($indexFile, $indexContent);
        echo "Route added: $route -> $controllerClass@index\n";
    }

    private function migrate() {
        $db = Database::getInstance();
        $migration = new Migration($db);
        $files = glob(__DIR__ . '/../../migrations/*.php');
        
        foreach ($files as $file) {
            $migration->run($file);
            echo "Migrated: " . basename($file) . "\n";
        }
    }

    private function rollback() {
        $db = Database::getInstance();
        $migration = new Migration($db);
        $files = glob(__DIR__ . '/../../migrations/*.php');

        foreach (array_reverse($files) as $file) {
            $migration->rollback($file);
            echo "Rolled back: " . basename($file) . "\n";
        }
    }

    private function createMigrationFromModel() {
        if (count($this->arguments) < 3) {
            echo "Usage: php cli.php db:migrate <model>\n";
            return;
        }

        $model = ucfirst($this->arguments[2]);
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
}

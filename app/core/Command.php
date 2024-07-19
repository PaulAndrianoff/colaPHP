<?php

class Command {
    private $arguments;

    public function __construct($arguments) {
        $this->arguments = $arguments;
    }

    public function run() {
        if (count($this->arguments) < 3) {
            echo "Usage: php cli.php create:route <route> <controller> <view>\n";
            return;
        }

        $action = $this->arguments[1];
        $route = $this->arguments[2];
        $controller = $this->arguments[3];
        $view = $this->arguments[4];

        if ($action === 'create:route') {
            $this->createRoute($route, $controller, $view);
        } else {
            echo "Unknown command: $action\n";
        }
    }

    private function createRoute($route, $controller, $view) {
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
}

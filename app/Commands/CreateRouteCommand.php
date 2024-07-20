<?php

class CreateRouteCommand {
    private $arguments;

    public function __construct($arguments) {
        $this->arguments = $arguments;
    }

    public function execute() {
        if (count($this->arguments) < 2) {
            echo "Usage: php cli.php create:route <route> <controller> <view>\n";
            return;
        }

        $route = $this->arguments[1];
        $controller = $this->arguments[2] ?? $route . 'Controller';
        $view = $this->arguments[3] ?? $route;

        $this->createController($controller, $view);
        $this->createView($view);
        $this->addRoute($route, $controller);
    }

    public function getSyntax() {
        return "php cli.php create:route <route> <controller> <view>";
    }

    private function createController($controller, $view) {
        $controllerName = ucfirst($controller);
        $controllerFile = __DIR__ . '/../Controllers/' . $controllerName . '.php';

        if (file_exists($controllerFile)) {
            echo "Controller already exists: $controller\n";
            return;
        }

        $content = "<?php\n\n";
        $content .= "class $controllerName extends Controller {\n";
        $content .= "    public function index() {\n";
        $content .= "        \$this->view('$view');\n";
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

        $routeDefinition = "\$router->get('/$route', '$controllerClass@index');\n";

        $indexContent = file_get_contents($indexFile);
        $indexContent = str_replace("\$router->run();", "$routeDefinition\$router->run();", $indexContent);

        file_put_contents($indexFile, $indexContent);
        echo "Route added: $route -> $controllerClass@index\n";
    }
}

<?php

namespace App\Core;

class Router
{
    private $routes = [
        'web' => [
            'GET' => [],
            'POST' => [],
            // Add other HTTP methods as needed
        ],
        'api' => [
            'GET' => [],
            'POST' => [],
            // Add other HTTP methods as needed
        ],
    ];

    /**
     * Register a GET route
     */
    public function get($uri, $controller, $isApi = false)
    {
        $this->registerRoute('GET', $uri, $controller, $isApi);
    }

    /**
     * Register a POST route
     */
    public function post($uri, $controller, $isApi = false)
    {
        $this->registerRoute('POST', $uri, $controller, $isApi);
    }

    /**
     * Register an API GET route
     */
    public function apiGet($uri, $controller)
    {
        $this->get($uri, $controller, true);
    }

    /**
     * Register an API POST route
     */
    public function apiPost($uri, $controller)
    {
        $this->post($uri, $controller, true);
    }

    /**
     * Core method to register a route
     */
    private function registerRoute($method, $uri, $controller, $isApi = false)
    {
        $routeType = $isApi ? 'api' : 'web';
        $this->routes[$routeType][$method][$this->formatRoute($uri)] = $controller;
    }

    /**
     * Run the router and dispatch the request
     */
    public function run()
    {
        $uri = $this->getUri();
        $method = $_SERVER['REQUEST_METHOD'];

        if (
            $this->dispatch($this->routes['web'][$method], $uri, false) ||
            $this->dispatch($this->routes['api'][$method], $uri, true)
        ) {
            return;
        }

        // Fallback to 404
        $this->callAction('ErrorController@notFound', []);
    }

    /**
     * Get the current URI, removing the script name from the request
     */
    private function getUri()
    {
        $uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));

        if ($scriptName !== '/' && strpos($uri, ltrim($scriptName, '/')) === 0) {
            $uri = substr($uri, strlen(ltrim($scriptName, '/')));
        }

        return '/' . trim($uri, '/');
    }

    /**
     * Format the route to ensure consistent formatting
     */
    private function formatRoute($uri)
    {
        return '/' . trim($uri, '/');
    }

    /**
     * Match the URI against a route pattern and extract parameters
     */
    private function match($route, $uri, &$params)
    {
        $route = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $route);
        $routePattern = "#^$route$#";

        if (preg_match($routePattern, $uri, $matches)) {
            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            return true;
        }

        return false;
    }

    /**
     * Call the appropriate controller and action for a matched route
     */
    private function callAction($controllerAction, $params, $isApi = false)
    {
        list($controller, $action) = explode('@', $controllerAction);

        $controller = 'App\\Controllers\\' . $controller;

        if (!class_exists($controller)) {
            throw new \Exception("Controller class $controller not found.");
        }

        $controllerInstance = new $controller;

        if (!method_exists($controllerInstance, $action)) {
            throw new \Exception("Method $action not found in controller $controller.");
        }

        if ($isApi) {
            header('Content-Type: application/json');
            echo json_encode(call_user_func_array([$controllerInstance, $action], $params));
        } else {
            call_user_func_array([$controllerInstance, $action], $params);
        }
    }

    /**
     * Dispatch the route, checking for matches and calling the action
     */
    private function dispatch($routes, $uri, $isApi)
    {
        foreach ($routes as $route => $controller) {
            if ($this->match($route, $uri, $params)) {
                $this->callAction($controller, $params, $isApi);
                return true;
            }
        }

        return false;
    }
}

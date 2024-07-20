<?php

class Router {
    private $routes = [
        'GET' => [],
        'POST' => [],
        // You can add more HTTP methods as needed
    ];
    private $apiRoutes = [
        'GET' => [],
        'POST' => [],
        // You can add more HTTP methods as needed
    ];

    public function get($uri, $controller) {
        $this->routes['GET'][$this->formatRoute($uri)] = $controller;
    }

    public function post($uri, $controller) {
        $this->routes['POST'][$this->formatRoute($uri)] = $controller;
    }

    public function apiGet($uri, $controller) {
        $this->apiRoutes['GET'][$this->formatRoute($uri)] = $controller;
    }

    public function apiPost($uri, $controller) {
        $this->routes['POST'][$this->formatRoute($uri)] = $controller;
    }

    public function run() {
        $uri = $this->getUri();
        $method = $_SERVER['REQUEST_METHOD'];

        if ($this->dispatch($this->routes[$method], $uri, false) ||
            $this->dispatch($this->apiRoutes[$method], $uri, true)) {
            return;
        }

        $this->callAction('ErrorController@notFound', []);
    }

    private function getUri() {
        $uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));

        if ($scriptName !== '/' && strpos($uri, ltrim($scriptName, '/')) === 0) {
            $uri = substr($uri, strlen(ltrim($scriptName, '/')));
        }
        return '/' . trim($uri, '/');
    }

    private function formatRoute($uri) {
        return '/' . trim($uri, '/');
    }

    private function match($route, $uri, &$params) {
        $route = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $route);
        $routePattern = "#^$route$#";

        if (preg_match($routePattern, $uri, $matches)) {
            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            return true;
        }

        return false;
    }

    private function callAction($controllerAction, $params, $isApi = false) {
        list($controller, $action) = explode('@', $controllerAction);
        require_once __DIR__ . '/../Controllers/' . $controller . '.php';
        $controller = new $controller;

        if ($isApi) {
            header('Content-Type: application/json');
            echo json_encode(call_user_func_array([$controller, $action], $params));
        } else {
            call_user_func_array([$controller, $action], $params);
        }
    }

    private function dispatch($routes, $uri, $isApi) {
        foreach ($routes as $route => $controller) {
            if ($this->match($route, $uri, $params)) {
                $this->callAction($controller, $params, $isApi);
                return true;
            }
        }
        return false;
    }
}

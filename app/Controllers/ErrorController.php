<?php

class ErrorController extends Controller {
    public function notFound() {
        http_response_code(404);
        require_once __DIR__ . '/../Views/404.php';
    }
}

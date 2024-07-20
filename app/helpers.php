<?php

require_once __DIR__ . '/../config.php';

if (!function_exists('dd')) {
    function dd(...$vars) {
        Debugger::dd(...$vars);
    }
}

if (!function_exists('getConfig')) {
    function getConfig($value) {
        $config = require __DIR__ . '/../config.php';
        return $config[$value];
    }
}

if (!function_exists('redirect')) {
    function redirect($route) {
        header('Location: ' . getConfig('url') . $route);
    }
}

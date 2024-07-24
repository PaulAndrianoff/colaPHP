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

if (!function_exists('isHash')) {
    function isHash($string) {
        $hashFormats = [
            'md5' => '/^[a-f0-9]{32}$/i',
            'sha1' => '/^[a-f0-9]{40}$/i',
            'sha256' => '/^[a-f0-9]{64}$/i'
        ];
        
        foreach ($hashFormats as $format => $pattern) {
            if (preg_match($pattern, $string)) {
                return true;
            }
        }
        
        return false;
    }
}
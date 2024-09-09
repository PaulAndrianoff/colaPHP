<?php

namespace App\core;

class Debugger {
    public static function enable() {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
    }

    public static function handleError($errno, $errstr, $errfile, $errline) {
        if (!(error_reporting() & $errno)) {
            return;
        }
        self::displayError("Error [$errno]: $errstr in $errfile on line $errline");
    }

    public static function handleException($exception) {
        self::displayError("Uncaught Exception: " . $exception->getMessage());
    }

    private static function displayError($message) {
        echo "<div style='border:1px solid red;padding:10px;margin:10px;'>";
        echo "<strong>Debug:</strong> $message";
        echo "</div>";
    }

    public static function dd(...$vars) {
        echo '<pre>';
        foreach ($vars as $var) {
            var_dump($var);
        }
        echo '</pre>';
        die();
    }
}

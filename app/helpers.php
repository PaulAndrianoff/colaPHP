<?php

if (!function_exists('dd')) {
    function dd(...$vars) {
        Debugger::dd(...$vars);
    }
}

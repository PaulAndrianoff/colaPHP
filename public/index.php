<?php

require_once __DIR__ . '/../app/helpers.php';
require_once __DIR__ . '/../vendor/autoload.php';

use App\core\Debugger;
use App\core\Router;

$config = require __DIR__ . '/../config.php';

if ($config['debug']) {
    Debugger::enable();
}

session_start();

$router = new Router();

require_once __DIR__ . '/../routes/web.php';
require_once __DIR__ . '/../routes/api.php';

$router->run();
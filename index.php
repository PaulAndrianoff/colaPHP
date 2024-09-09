<?php

require_once __DIR__ . '/app/helpers.php';
require_once __DIR__ . '/app/core/Router.php';
require_once __DIR__ . '/app/core/Controller.php';
require_once __DIR__ . '/app/core/Database.php';
require_once __DIR__ . '/app/core/BaseModel.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/app/core/Debugger.php';

$config = require __DIR__ . '/config.php';

if ($config['debug']) {
    Debugger::enable();
}

session_start();

$router = new Router();

require_once __DIR__ . '/routes/web.php';
require_once __DIR__ . '/routes/api.php';

$router->run();

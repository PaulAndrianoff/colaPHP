<?php

require_once __DIR__ . '/app/core/Command.php';
require_once __DIR__ . '/config.php';

$config = require __DIR__ . '/config.php';

if ($config['debug']) {
    Debugger::enable();
}

$command = new Command($argv);
$command->run();

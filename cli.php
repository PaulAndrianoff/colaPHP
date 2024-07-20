<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/app/core/Database.php';
require_once __DIR__ . '/app/core/Migration.php';

$config = require __DIR__ . '/config.php';

if ($config['debug']) {
    Debugger::enable();
}

$command = $argv[1] ?? null;
$arguments = array_slice($argv, 1);

if ($command) {
    $commandClass = commandToClassName($command);
    $commandFile = __DIR__ . "/app/Commands/{$commandClass}.php";

    if (file_exists($commandFile)) {
        require_once $commandFile;
        $commandInstance = new $commandClass($arguments);
        $commandInstance->execute();
    } else {
        echo "Command not found: $command\n";
    }
} else {
    echo "Usage: php cli.php [command] [parameters]\n";
}

function commandToClassName($command) {
    $parts = explode(':', $command);
    $parts = array_map('ucfirst', $parts);
    return implode('', $parts) . 'Command';
}

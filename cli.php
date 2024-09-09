<?php

// Use Composer's autoloader to load all necessary classes automatically
require_once __DIR__ . '/vendor/autoload.php';

use App\Core\Database;
use App\Core\Migration;
use App\Core\Debugger;

$config = require __DIR__ . '/config.php';

// Enable debugger if in debug mode
if ($config['debug']) {
    Debugger::enable();
}

$command = $argv[1] ?? null;
$arguments = array_slice($argv, 1);

if ($command) {
    // Convert command name to class name (assuming commands follow PSR-4 structure)
    $commandClass = commandToClassName($command);
    $commandNamespace = "App\\Commands\\{$commandClass}";
    
    // Try to instantiate the command class
    if (class_exists($commandNamespace)) {
        $commandInstance = new $commandNamespace($arguments);
        $commandInstance->execute();
    } else {
        echo "Command not found: $command\n";
    }
} else {
    echo "Usage: php cli.php [command] [parameters]\n";
}

/**
 * Convert a CLI command like "db:migrate" to a class name.
 * Example: "db:migrate" becomes "DbMigrateCommand".
 */
function commandToClassName($command) {
    $parts = explode(':', $command);
    $parts = array_map('ucfirst', $parts);
    return implode('', $parts) . 'Command';
}

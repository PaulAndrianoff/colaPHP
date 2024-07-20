<?php

class MigrateCommand {
    private $arguments;

    public function __construct($arguments) {
        $this->arguments = $arguments;
    }

    public function execute() {
        $db = Database::getInstance();
        $migration = new Migration($db);
        $files = glob(__DIR__ . '/../../migrations/*.php');
        
        foreach ($files as $file) {
            $migration->run($file);
            echo "Migrated: " . basename($file) . "\n";
        }
    }

    public function getSyntax() {
        return "php cli.php migrate";
    }
}

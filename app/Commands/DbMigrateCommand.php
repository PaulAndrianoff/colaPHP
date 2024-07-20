<?php

class DbMigrateCommand {
    private $arguments;

    public function __construct($arguments) {
        $this->arguments = $arguments;
    }

    public function execute() {
        $db = Database::getInstance();
        $migration = new Migration($db);
        $files = glob(__DIR__ . '/../../migrations/*.php');
        
        foreach ($files as $file) {
            try {
                $migration->run($file);
                echo "Migrated: " . basename($file) . "\n";
            } catch (\Throwable $th) {
                echo "Not migrated: " . basename($file) . "\n";
            }
        }
    }

    public function getSyntax() {
        return "php cli.php db:migrate";
    }
}

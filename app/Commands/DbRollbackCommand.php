<?php

class DbRollbackCommand {
    private $arguments;

    public function __construct($arguments) {
        $this->arguments = $arguments;
    }

    public function execute() {
        $db = Database::getInstance();
        $migration = new Migration($db);
        $files = glob(__DIR__ . '/../../migrations/*.php');

        foreach (array_reverse($files) as $file) {
            $migration->rollback($file);
            echo "Rolled back: " . basename($file) . "\n";
        }
    }

    public function getSyntax() {
        return "php cli.php db:rollback";
    }
}

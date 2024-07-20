<?php

class Migration {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function run($file) {
        require_once $file;
        $migrationClass = $this->getClassName($file);
        if (!class_exists($migrationClass)) {
            throw new Exception("Class '$migrationClass' not found in file '$file'");
        }
        $migration = new $migrationClass($this->db);
        $migration->up();
    }

    public function rollback($file) {
        require_once $file;
        $migrationClass = $this->getClassName($file);
        if (!class_exists($migrationClass)) {
            throw new Exception("Class '$migrationClass' not found in file '$file'");
        }
        $migration = new $migrationClass($this->db);
        $migration->down();
    }

    private function getClassName($file) {
        $basename = basename($file, '.php');
        $parts = explode('_', $basename);
        array_shift($parts); // remove the date part
        $className = implode('', array_map('ucfirst', $parts));
        return $className;
    }
}

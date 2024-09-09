<?php

namespace App\core;

use PDO;
use PDOException;

class Database {
    private static $instance = null;
    private $pdo;
    private $stmt;

    private function __construct() {
        $config = require __DIR__ . '/../../config.php';
        $db = $config['db'];
        
        try {
            $dsn = 'mysql:host=' . $db['host'] . ';dbname=' . $db['dbname'];
            $this->pdo = new PDO($dsn, $db['user'], $db['pass']);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die('Database connection failed: ' . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function query($sql, $params = []) {
        $this->stmt = $this->pdo->prepare($sql);
        $this->stmt->execute($params);
        return $this->stmt;
    }

    public function fetch($sql, $params = []) {
        $this->stmt = $this->pdo->prepare($sql);
        $this->stmt->execute($params);
        return $this->stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function fetchAll($sql, $params = []) {
        $this->stmt = $this->pdo->prepare($sql);
        $this->stmt->execute($params);
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }
}

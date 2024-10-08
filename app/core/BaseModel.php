<?php

namespace App\core;

use App\core\Database;

class BaseModel {
    protected $db;
    protected $table;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    protected function query($sql, $params = []) {
        return $this->db->query($sql, $params);
    }

    protected function fetch($sql, $params = []) {
        return $this->db->fetch($sql, $params);
    }

    protected function fetchAll($sql, $params = []) {
        return $this->db->fetchAll($sql, $params);
    }

    protected function lastInsertId() {
        return $this->db->lastInsertId();
    }

    public function all() {
        $sql = "SELECT * FROM " . $this->getTable();
        return $this->fetchAll($sql);
    }

    public function find($id) {
        $sql = "SELECT * FROM " . $this->getTable() . " WHERE id = :id";
        return $this->fetch($sql, ['id' => $id]);
    }

    public function create($data) {
        $newData = $this->formValidation($data);
        if (!empty($newData['errors'])) {
            return $newData['errors'];
        }

        $columns = implode(',', array_keys($data));
        $values = ':' . implode(',:', array_keys($data));
        $sql = "INSERT INTO " . $this->getTable() . " ($columns) VALUES ($values)";
        return $this->query($sql, $data);
    }

    public function update($id, $data) {
        $newData = $this->formValidation($data);
        if (!empty($newData['errors'])) {
            return $newData['errors'];
        }

        $fields = '';
        foreach ($data as $key => $value) {
            $fields .= "$key = :$key,";
        }
        $fields = rtrim($fields, ',');
        $sql = "UPDATE " . $this->getTable() . " SET $fields WHERE id = :id";
        return $this->query($sql, array_merge($data, ['id' => $id]));
    }

    public function delete($id) {
        $sql = "DELETE FROM " . $this->getTable() . " WHERE id = :id";
        return $this->query($sql, ['id' => $id]);
    }

    private function getTable() {
        $class = $this->table;
        return strtolower($class);
    }

    public function formValidation($data) {
        // Default validation logic, should be overridden by child classes
        return [];
    }
}

<?php

require_once __DIR__ . '/../core/BaseModel.php';

class User extends BaseModel {
    protected $table = 'users';

    public function getUserById($id) {
        return $this->fetch("SELECT * FROM {$this->table} WHERE id = :id", ['id' => $id]);
    }

    public function getAllUsers() {
        return $this->fetchAll("SELECT * FROM {$this->table}");
    }

    public function createUser($data) {
        $sql = "INSERT INTO {$this->table} (name, email) VALUES (:name, :email)";
        $this->query($sql, $data);
        return $this->lastInsertId();
    }
}

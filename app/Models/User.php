<?php

require_once __DIR__ . '/../core/BaseModel.php';

class User extends BaseModel {
    /**
     * @table(users)
     */
    protected $table = 'users';

    /**
    * @column(id)
    * @type(int)
    * @key(primary;auto_incr)
    **/
    public $id;

    /**
    * @column(name)
    * @type(varchar)
    * @length(255)
    * @not_null
    **/
    public $name;

    /**
    * @column(email)
    * @type(varchar)
    * @length(255)
    * @not_null
    **/
    public $email;

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

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
    * @not_editable
    **/
    public $id;

    /**
    * @column(username)
    * @type(varchar)
    * @length(255)
    * @not_null
    **/
    public $username;

    /**
    * @column(password)
    * @type(varchar)
    * @length(255)
    * @not_null
    * @formType(password)
    **/
    public $password;

    /**
    * @column(created_at)
    * @type(TIMESTAMP)
    * @default(CURRENT_TIMESTAMP)
    * @not_null
    * @formType(datetime-local)
    **/
    public $created_at;

    public function getUserById($id) {
        return $this->fetch("SELECT * FROM {$this->table} WHERE id = :id", ['id' => $id]);
    }

    public function getAllUsers() {
        return $this->fetchAll("SELECT * FROM {$this->table}");
    }

    public function createUser($data) {
        $sql = "INSERT INTO {$this->table} (names, email) VALUES (:names, :email)";
        $this->query($sql, $data);
        return $this->lastInsertId();
    }

    public function checkCredentials($username, $password) {
        $user = $this->fetch("SELECT * FROM {$this->table} WHERE username = :username", ['username' => $username]);

        if ($user && password_verify($password, $user['password'])) {
            return true;
        }

        return false;
    }
}

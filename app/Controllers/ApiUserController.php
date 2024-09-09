<?php

namespace App\Controllers;

use App\Models\User;

class ApiUserController {
    public function show($id) {
        $userModel = new User();
        $user = $userModel->getUserById($id);

        if ($user) {
            return $user;
        } else {
            return 'not found';
        }
    }

    public function index() {
        $userModel = new User();
        $users = $userModel->getAllUsers();
        return $users;
    }
}

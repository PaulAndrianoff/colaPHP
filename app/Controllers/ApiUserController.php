<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class ApiUserController extends Controller
{
    /**
     * Display a single user by ID
     */
    public function show($id)
    {
        $userModel = new User();
        $user = $userModel->getUserById($id);

        if ($user) {
            return $this->jsonResponse($user, 200);
        }

        return $this->jsonResponse(['message' => 'User not found'], 404);
    }

    /**
     * Display a list of all users
     */
    public function index()
    {
        $userModel = new User();
        $users = $userModel->getAllUsers();

        return $this->jsonResponse($users, 200);
    }
}

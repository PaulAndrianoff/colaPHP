<?php

require_once __DIR__ . '/../Models/User.php';

class UserController extends Controller {
    public function show($id) {
        $userModel = new User();
        $user = $userModel->getUserById($id);

        if ($user) {
            $this->view('user', ['user' => $user]);
        } else {
            redirect('/users-not-found');
        }
    }

    public function index() {
        $userModel = new User();
        $users = $userModel->getAllUsers();
        $this->view('users', ['users' => $users]); // Ensure users variable is passed
    }
}

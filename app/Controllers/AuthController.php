<?php

namespace App\Controllers;

use App\core\Controller;
use App\Models\User;

class AuthController extends Controller {
    public function index() {
        $this->view('admin/login', ['formRoute' => 'login']);
    }

    public function login() {
        $username = $_POST['username'];
        $password = $_POST['password'];
        
        $user = new User();
        if ($user->checkCredentials($username, $password)) {
            $_SESSION['admin_logged_in'] = true;
            redirect('/admin');
        } else {
            $error = "Invalid credentials";
            require_once __DIR__ . '/../views/admin/login.php';
            $this->view('admin/login', ['formRoute' => 'login']);
        }
    }

    public function logout() {
        unset($_SESSION['admin_logged_in']);
        redirect('/admin/login');
    }
}

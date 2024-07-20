<?php

class AdminController extends Controller {
    public function __construct() {
        // Ensure the user is authenticated
        if (!isset($_SESSION['admin_logged_in'])) {
            redirect('/admin/login');
            exit;
        }
    }

    public function index() {
        $this->view('admin/index', ['logout' => 'admin/logout']);
    }
}

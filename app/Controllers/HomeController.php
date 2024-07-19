<?php

class HomeController extends Controller {
    public function index() {
        $data = ['message' => 'Welcome to colaPHP!'];
        $this->view('home', $data);
    }
}

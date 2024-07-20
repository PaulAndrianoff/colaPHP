<?php

require_once __DIR__ . '/app/helpers.php';
require_once __DIR__ . '/app/core/Router.php';
require_once __DIR__ . '/app/core/Controller.php';
require_once __DIR__ . '/app/core/Database.php';
require_once __DIR__ . '/app/core/BaseModel.php';
require_once __DIR__ . '/config.php'; // Load configuration
require_once __DIR__ . '/app/core/Debugger.php'; // Include Debugger

$config = require __DIR__ . '/config.php';

if ($config['debug']) {
    Debugger::enable();
}

session_start();

$router = new Router();

$router->get('/', 'HomeController@index');
$router->get('/user/{id}', 'UserController@show');
$router->get('/users', 'UserController@index');
$router->apiGet('/api/user/{id}', 'ApiUserController@show');

$router->get('/admin', 'AdminController@index');
$router->get('/admin/login', 'AuthController@index');
$router->post('/admin/login', 'AuthController@login');
$router->get('/admin/logout', 'AuthController@logout');
$router->run();

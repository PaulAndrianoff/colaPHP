<?php

$router->get('/', 'HomeController@index');
$router->get('/user/{id}', 'UserController@show');
$router->get('/users', 'UserController@index');

// CRUD access for admin panel
$router->get('/admin', 'AdminController@index');
$router->get('/admin/login', 'AuthController@index');
$router->post('/admin/login', 'AuthController@login');
$router->get('/admin/logout', 'AuthController@logout');

// CRUD operations for admin panel
$router->get('/admin/models/{model}', 'AdminController@list');
$router->get('/admin/models/{model}/create', 'AdminController@createForm');
$router->post('/admin/models/{model}/create', 'AdminController@create');
$router->get('/admin/models/{model}/edit/{id}', 'AdminController@editForm');
$router->post('/admin/models/{model}/edit/{id}', 'AdminController@edit');
$router->get('/admin/models/{model}/delete/{id}', 'AdminController@delete');
$router->get('/admin/configuration', 'AdminController@configurationPanel');
$router->post('/admin/configuration', 'AdminController@configureStyle');
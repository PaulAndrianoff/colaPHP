<?php

$router->apiGet('/api/user/all', 'ApiUserController@index');
$router->apiGet('/api/user/{id}', 'ApiUserController@show');
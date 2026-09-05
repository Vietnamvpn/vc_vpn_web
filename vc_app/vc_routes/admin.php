<?php

use VcCore\Application;

$app = Application::getInstance();
$router = $app->getRouter();

// Định tuyến cho khu vực Admin sử dụng AuthController (có dấu / ở đầu)
$router->get('/admin/login', 'AuthController@showLoginForm');
$router->post('/admin/login', 'AuthController@processLogin');
$router->get('/admin/logout', 'AuthController@logout');

// Định tuyến được bảo vệ bởi AdminMiddleware (GỌI MIDDLEWARE TRƯỚC)
$router->middleware('AdminMiddleware')->get('/admin', 'AdminDashboardController@index');
$router->middleware('AdminMiddleware')->get('/admin/dashboard', 'AdminDashboardController@index');
<?php

use VcCore\Application;

$app = Application::getInstance();
$router = $app->getRouter();

// Định tuyến cho khu vực Admin sử dụng AuthController
$router->get('admin/login', 'AuthController@showLoginForm');
$router->post('admin/login', 'AuthController@processLogin');
$router->get('admin/logout', 'AuthController@logout');

// Định tuyến được bảo vệ bởi AdminMiddleware (Bắt buộc phải đăng nhập quản trị)
$router->get('admin', 'AdminDashboardController@index')->middleware('AdminMiddleware');
$router->get('admin/dashboard', 'AdminDashboardController@index')->middleware('AdminMiddleware');
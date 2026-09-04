<?php
/**
 * vc_app/Routes/web.php
 * Web routes definition for application and admin panel.
 */

// Simple routing mechanism for vc_vpn_web
$uri = trim($requestUri, '/');
$method = $requestMethod;

// Basic Router Dispatcher Mapping
switch ($uri) {
    case '':
    case 'index.php':
        echo "Welcome to VC VPN Web. <a href='/admin/login'>Go to Admin Login</a>";
        break;

    case 'admin/login':
        $controllerPath = APP_BASE_PATH . '/vc_app/Controllers/Admin/AuthController.php';
        if (file_exists($controllerPath)) {
            require_once $controllerPath;
            $controller = new \VcApp\Controllers\Admin\AuthController();
            if ($method === 'POST') {
                $controller->login();
            } else {
                $controller->showLoginForm();
            }
        } else {
            header("HTTP/1.0 404 Not Found");
            echo "Admin AuthController not found.";
        }
        break;

    case 'admin/logout':
        $controllerPath = APP_BASE_PATH . '/vc_app/Controllers/Admin/AuthController.php';
        if (file_exists($controllerPath)) {
            require_once $controllerPath;
            $controller = new \VcApp\Controllers\Admin\AuthController();
            $controller->logout();
        }
        break;

    case 'admin/dashboard':
        $controllerPath = APP_BASE_PATH . '/vc_app/Controllers/Admin/DashboardController.php';
        if (file_exists($controllerPath)) {
            require_once $controllerPath;
            $controller = new \VcApp\Controllers\Admin\DashboardController();
            $controller->index();
        } else {
            header("HTTP/1.0 404 Not Found");
            echo "Admin DashboardController not found.";
        }
        break;

    default:
        header("HTTP/1.0 404 Not Found");
        echo "404 Page Not Found.";
        break;
}
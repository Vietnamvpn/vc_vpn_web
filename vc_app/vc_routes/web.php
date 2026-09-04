<?php
/**
 * vc_app/vc_routes/web.php
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
        $controllerPath = APP_BASE_PATH . '/vc_app/vc_controllers/AuthController.php';
        if (file_exists($controllerPath)) {
            require_once $controllerPath;
            $controller = new \VcApp\Controllers\AuthController();
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
        $controllerPath = APP_BASE_PATH . '/vc_app/vc_controllers/AuthController.php';
        if (file_exists($controllerPath)) {
            require_once $controllerPath;
            $controller = new \VcApp\Controllers\AuthController();
            $controller->logout();
        }
        break;

    case 'admin/dashboard':
        $dashboardPath = APP_BASE_PATH . '/vc_admin/dashboard.php';
        if (file_exists($dashboardPath)) {
            require_once $dashboardPath;
        } else {
            header("HTTP/1.0 404 Not Found");
            echo "Admin Dashboard not found.";
        }
        break;

    default:
        header("HTTP/1.0 404 Not Found");
        echo "404 Page Not Found.";
        break;
}
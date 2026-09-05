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
        $homePath = APP_BASE_PATH . '/vc_views/vc_public/home.php';
        if (file_exists($homePath)) {
            require_once $homePath;
        } else {
            header("HTTP/1.0 404 Not Found");
            echo "Home page not found.";
        }
        break;

    default:
        header("HTTP/1.0 404 Not Found");
        echo "404 Page Not Found.";
        break;
}
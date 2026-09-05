<?>
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
        $viewFile = APP_BASE_PATH . '/vc_views/vc_public/home.php';
        break;

    case 'pricing':
        $viewFile = APP_BASE_PATH . '/vc_views/vc_public/pricing.php';
        break;

    case 'features':
        $viewFile = APP_BASE_PATH . '/vc_views/vc_public/features.php';
        break;

    case 'faq':
        $viewFile = APP_BASE_PATH . '/vc_views/vc_public/faq.php';
        break;

    case 'contact':
        $viewFile = APP_BASE_PATH . '/vc_views/vc_public/contact.php';
        break;

    case 'login':
        $viewFile = APP_BASE_PATH . '/vc_views/vc_public/login.php';
        break;

    case 'register':
        $viewFile = APP_BASE_PATH . '/vc_views/vc_public/register.php';
        break;

    case 'forgot-password':
        $viewFile = APP_BASE_PATH . '/vc_views/vc_public/forgot-password.php';
        break;

    case 'reset-password':
        $viewFile = APP_BASE_PATH . '/vc_views/vc_public/reset-password.php';
        break;

    case 'verify-email':
        $viewFile = APP_BASE_PATH . '/vc_views/vc_public/verify-email.php';
        break;

    default:
        $viewFile = null;
        break;
}

// Kiểm tra và hiển thị trang tương ứng
if ($viewFile && file_exists($viewFile)) {
    require_once $viewFile;
} else {
    header("HTTP/1.0 404 Not Found");
    echo "404 Page Not Found.";
}
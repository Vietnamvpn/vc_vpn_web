<?php
/**
 * vc_public/index.php
 * Public entry point for vc_vpn_web application.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('APP_BASE_PATH', dirname(__DIR__));

if (file_exists(APP_BASE_PATH . '/vendor/autoload.php')) {
    require_once APP_BASE_PATH . '/vendor/autoload.php';
} else {
    die("Autoloader not found. Please run 'composer install'.");
}

if (file_exists(APP_BASE_PATH . '/.env')) {
    $lines = file(APP_BASE_PATH . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
        $_SERVER[trim($name)] = trim($value);
    }
}

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

$routesFile = APP_BASE_PATH . '/vc_app/vc_routes/web.php';
if (file_exists($routesFile)) {
    require_once $routesFile;
} else {
    header("HTTP/1.0 404 Not Found");
    echo "Routes configuration file missing.";
    exit;
}
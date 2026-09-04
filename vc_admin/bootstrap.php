<?php
/**
 * Shared bootstrap for the standalone admin entry points.
 */

declare(strict_types=1);

if (!defined('APP_BASE_PATH')) {
    define('APP_BASE_PATH', dirname(__DIR__));
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
        'use_strict_mode' => true,
    ]);
}

$autoloadPath = APP_BASE_PATH . '/vendor/autoload.php';
if (is_file($autoloadPath)) {
    require_once $autoloadPath;
}

$environmentPath = APP_BASE_PATH . '/.env';
if (is_file($environmentPath)) {
    foreach (file($environmentPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) {
            continue;
        }

        [$name, $value] = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        if (strlen($value) >= 2 && $value[0] === '"' && substr($value, -1) === '"') {
            $value = substr($value, 1, -1);
        }

        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
    }
}

function vc_admin_database(): PDO
{
    static $pdo;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
    $port = $_ENV['DB_PORT'] ?? '5432';
    $database = $_ENV['DB_DATABASE'] ?? '';
    $username = $_ENV['DB_USERNAME'] ?? '';
    $password = $_ENV['DB_PASSWORD'] ?? '';

    if ($database === '' || $username === '') {
        throw new RuntimeException('Database configuration is missing. Configure DB_DATABASE and DB_USERNAME in .env.');
    }

    $pdo = new PDO(
        "pgsql:host={$host};port={$port};dbname={$database}",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

    return $pdo;
}

function vc_admin_is_authenticated(): bool
{
    return ($_SESSION['admin_logged_in'] ?? false) === true
        && isset($_SESSION['admin_user_id']);
}

function vc_admin_url(string $path = ''): string
{
    $path = trim($path, '/');

    return '/admin' . ($path === '' ? '' : '/' . $path);
}

function vc_admin_hash_url(string $path = 'login'): string
{
    return '/#/' . trim($path, '/');
}

function vc_admin_require_authentication(): void
{
    if (!vc_admin_is_authenticated()) {
        header('Location: ' . vc_admin_url('login'));
        exit;
    }
}

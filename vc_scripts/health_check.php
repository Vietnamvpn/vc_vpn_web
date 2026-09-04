<?php
/**
 * vc_scripts/health_check.php
 * System health check verification script.
 */

if (php_sapi_name() !== 'cli') {
    die("This script can only be run from the command line.");
}

require_once __DIR__ . '/../vendor/autoload.php';

if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

$dbHost = $_ENV['DB_HOST'] ?? '127.0.0.1';
    $dbPort = $_ENV['DB_PORT'] ?? '3306';
$dbName = $_ENV['DB_DATABASE'] ?? '';
$dbUser = $_ENV['DB_USERNAME'] ?? '';
$dbPass = $_ENV['DB_PASSWORD'] ?? '';

$status = true;

// Check Database Connection
try {
    $dsn = "mysql:host=$dbHost;port=$dbPort;dbname=$dbName;charset=utf8mb4";
    $pdo = new PDO($dsn, $dbUser, $dbPass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo "[OK] Database connection is healthy.\n";
} catch (PDOException $e) {
    echo "[FAIL] Database connection error: " . $e->getMessage() . "\n";
    $status = false;
}

// Check Storage Directories Write Permissions
$storageDirs = [
    __DIR__ . '/../vc_storage/vc_cache',
    __DIR__ . '/../vc_storage/vc_sessions',
    __DIR__ . '/../vc_storage/vc_invoices',
    __DIR__ . '/../vc_storage/vc_exports',
    __DIR__ . '/../vc_storage/vc_temp',
    __DIR__ . '/../vc_logs'
];

foreach ($storageDirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
    if (is_writable($dir)) {
        echo "[OK] Directory writable: " . basename($dir) . "\n";
    } else {
        echo "[FAIL] Directory not writable: " . basename($dir) . "\n";
        $status = false;
    }
}

exit($status ? 0 : 1);
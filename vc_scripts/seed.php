<?php
/**
 * vc_scripts/seed.php
 * Executes database seeders to populate initial application data.
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
$dbPort = $_ENV['DB_PORT'] ?? '5432';
$dbName = $_ENV['DB_DATABASE'] ?? '';
$dbUser = $_ENV['DB_USERNAME'] ?? '';
$dbPass = $_ENV['DB_PASSWORD'] ?? '';

try {
    $dsn = "pgsql:host=$dbHost;port=$dbPort;dbname=$dbName";
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    echo "Successfully connected to PostgreSQL database.\n";
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage() . "\n");
}

$seedsPath = __DIR__ . '/../vc_database/vc_seeds';
if (is_dir($seedsPath)) {
    $seedFiles = glob($seedsPath . '/*.php');
    foreach ($seedFiles as $file) {
        echo "Running seeder: " . basename($file) . "...\n";
        try {
            include $file;
        } catch (Exception $e) {
            echo "Error running seeder " . basename($file) . ": " . $e->getMessage() . "\n";
        }
    }
    echo "All database seeds executed successfully.\n";
} else {
    echo "Seeds directory not found.\n";
}
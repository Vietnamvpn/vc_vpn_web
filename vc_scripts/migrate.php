<?php
/**
 * vc_scripts/migrate.php
 * Handles database migrations update for MySQL/PostgreSQL.
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

try {
    $dsn = "mysql:host=$dbHost;port=$dbPort;dbname=$dbName;charset=utf8mb4";
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    echo "Successfully connected to MySQL database.\n";
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage() . "\n");
}

$migrationsPath = __DIR__ . '/../vc_database/vc_migrations';
if (is_dir($migrationsPath)) {
    $files = glob($migrationsPath . '/*.sql');
    sort($files);
    foreach ($files as $file) {
        echo "Executing migration: " . basename($file) . "...\n";
        $sql = file_get_contents($file);
        
        // Tự động loại bỏ các lệnh PostgreSQL không tương thích với MySQL
        $sql = preg_replace('/CREATE\s+EXTENSION\s+IF\s+NOT\s+EXISTS\s+pgcrypto\s*;/i', '', $sql);

        try {
            $pdo->exec($sql);
        } catch (PDOException $e) {
            fwrite(STDERR, "Migration failed in " . basename($file) . ": " . $e->getMessage() . "\n");
            exit(1);
        }
    }
    echo "Database migrations updated successfully.\n";
} else {
    echo "Migrations directory not found.\n";
}
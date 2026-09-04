<?php
/**
 * vc_scripts/restore.php
 * Automated MySQL database restoration script.
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

$backupFile = $argv[1] ?? '';
if (empty($backupFile) || !file_exists($backupFile)) {
    die("Error: Please provide a valid backup file path as an argument.\n");
}

$dbHost = $_ENV['DB_HOST'] ?? '127.0.0.1';
$dbPort = $_ENV['DB_PORT'] ?? '3306';
$dbName = $_ENV['DB_DATABASE'] ?? '';
$dbUser = $_ENV['DB_USERNAME'] ?? '';
$dbPass = $_ENV['DB_PASSWORD'] ?? '';

$command = sprintf(
    "MYSQL_PWD=%s mysql -h %s -P %s -u %s %s < %s",
    escapeshellarg($dbPass),
    escapeshellarg($dbHost),
    escapeshellarg($dbPort),
    escapeshellarg($dbUser),
    escapeshellarg($dbName),
    escapeshellarg($backupFile)
);

exec($command, $output, $resultCode);

if ($resultCode === 0) {
    echo "Database restored successfully from $backupFile.\n";
} else {
    echo "Database restoration failed with exit code $resultCode.\n";
}
<?php
/**
 * vc_scripts/backup.php
 * Automated PostgreSQL database and storage backup script.
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

$backupDir = __DIR__ . '/../vc_storage/vc_exports';
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0775, true);
}

$filename = 'backup_' . $dbName . '_' . date('Y-m-d_H-i-s') . '.sql';
$filepath = $backupDir . '/' . $filename;

$command = sprintf(
    "PGPASSWORD=%s pg_dump -h %s -p %s -U %s -d %s -F c -b -v -f %s",
    escapeshellarg($dbPass),
    escapeshellarg($dbHost),
    escapeshellarg($dbPort),
    escapeshellarg($dbUser),
    escapeshellarg($dbName),
    escapeshellarg($filepath)
);

exec($command, $output, $resultCode);

if ($resultCode === 0) {
    echo "Database backup created successfully: $filepath\n";
} else {
    echo "Database backup failed with exit code $resultCode.\n";
}
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

// Tạo bảng ghi lại lịch sử migration nếu chưa có
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS migrations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        migration VARCHAR(255) NOT NULL UNIQUE,
        executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    $stmt = $pdo->query("SELECT migration FROM migrations");
    $executedMigrations = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $executedMigrations = [];
}

$migrationsPath = __DIR__ . '/../vc_database/vc_migrations';
if (is_dir($migrationsPath)) {
    $files = glob($migrationsPath . '/*.sql');
    sort($files);
    $executedCount = 0;

    foreach ($files as $file) {
        $filename = basename($file);

        // Bỏ qua nếu migration này đã được thực thi trước đó
        if (in_array($filename, $executedMigrations)) {
            continue;
        }

        echo "Executing migration: " . $filename . "...\n";
        $sql = file_get_contents($file);
        
        // Tự động loại bỏ các lệnh PostgreSQL không tương thích với MySQL
        $sql = preg_replace('/CREATE\s+EXTENSION\s+IF\s+NOT\s+EXISTS\s+pgcrypto\s*;/i', '', $sql);

        try {
            $pdo->exec($sql);
            
            // Ghi nhận migration đã chạy thành công vào database
            $insertStmt = $pdo->prepare("INSERT INTO migrations (migration) VALUES (?)");
            $insertStmt->execute([$filename]);
            $executedCount++;
            
        } catch (PDOException $e) {
            fwrite(STDERR, "Migration failed in " . $filename . ": " . $e->getMessage() . "\n");
            exit(1);
        }
    }
    
    if ($executedCount > 0) {
        echo "Database migrations updated successfully ($executedCount new migrations executed).\n";
    } else {
        echo "Database is already up to date. No new migrations to execute.\n";
    }
} else {
    echo "Migrations directory not found.\n";
}
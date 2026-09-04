<?php
/**
 * vc_scripts/install.php
 * Automated post-installation script to run migrations, seeds, and create initial admin.
 */

if (php_sapi_name() !== 'cli') {
    die("This script can only be run from the command line.");
}

require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables from .env
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

// 1. Run Migrations from vc_database/vc_migrations/
$migrationsPath = __DIR__ . '/../vc_database/vc_migrations';
if (is_dir($migrationsPath)) {
    $files = glob($migrationsPath . '/*.sql');
    sort($files);
    foreach ($files as $file) {
        echo "Running migration: " . basename($file) . "...\n";
        $sql = file_get_contents($file);
        try {
            $pdo->exec($sql);
        } catch (PDOException $e) {
            echo "Migration error in " . basename($file) . ": " . $e->getMessage() . "\n";
        }
    }
}

// 2. Run Seeds from vc_database/vc_seeds/
$seedsPath = __DIR__ . '/../vc_database/vc_seeds';
if (is_dir($seedsPath)) {
    $seedFiles = glob($seedsPath . '/*.php');
    foreach ($seedFiles as $file) {
        echo "Executing seeder: " . basename($file) . "...\n";
        include_once $file;
    }
}

// 3. Create Super Admin Account interacting with users, roles, and user_roles tables[cite: 1]
echo "Creating default Super Admin account...\n";
$adminEmail = 'admin@vc-vpn.local';
$adminUsername = 'admin';
$adminPass = password_hash('Admin@123456', PASSWORD_BCRYPT);

$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$adminEmail]);
if (!$stmt->fetch()) {
    $insertUser = $pdo->prepare("INSERT INTO users (email, username, password_hash, full_name, status, email_verified_at) VALUES (?, ?, ?, 'Super Administrator', 'active', NOW()) RETURNING id");
    $insertUser->execute([$adminEmail, $adminUsername, $adminPass]);
    $user = $insertUser->fetch();
    $userId = $user['id'];

    // Assign super_admin role[cite: 1]
    $roleStmt = $pdo->prepare("SELECT id FROM roles WHERE name = 'super_admin'");
    $roleStmt->execute();
    $role = $roleStmt->fetch();
    if ($role) {
        $assignRole = $pdo->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?) ON CONFLICT DO NOTHING");
        $assignRole->execute([$userId, $role['id']]);
    }
    echo "Super Admin created successfully (Email: $adminEmail, Password: Admin@123456).\n";
} else {
    echo "Super Admin already exists. Skipping creation.\n";
}

echo "System initialization script completed successfully.\n";
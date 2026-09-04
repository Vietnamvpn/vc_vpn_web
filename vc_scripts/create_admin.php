<?php
/**
 * vc_scripts/create_admin.php
 * Script to create an administrator account via CLI.
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
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage() . "\n");
}

$email = getenv('VC_ADMIN_EMAIL') ?: ($argv[1] ?? 'admin@example.com');
$username = getenv('VC_ADMIN_USERNAME') ?: ($argv[2] ?? 'admin');
$password = getenv('VC_ADMIN_PASSWORD') ?: ($argv[3] ?? 'Admin@123456');

$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
$stmt->execute([$email, $username]);
$existingUser = $stmt->fetch();

if ($existingUser) {
    $userId = $existingUser['id'];
    echo "User already exists; ensuring administrator role is assigned.\n";
} else {
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);
    $insert = $pdo->prepare("INSERT INTO users (email, username, password_hash, full_name, status, email_verified_at) VALUES (?, ?, ?, 'Administrator', 'active', NOW()) RETURNING id");
    $insert->execute([$email, $username, $passwordHash]);
    $user = $insert->fetch();
    $userId = $user['id'];
}

$roleStmt = $pdo->prepare("SELECT id FROM roles WHERE name = 'admin'");
$roleStmt->execute();
$role = $roleStmt->fetch();

if ($role) {
    $assign = $pdo->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?) ON CONFLICT DO NOTHING");
    $assign->execute([$userId, $role['id']]);
    echo "Administrator account is ready (Username: $username, Email: $email).\n";
} else {
    echo "User created, but 'admin' role was not found in the database.\n";
}
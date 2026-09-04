<?php
/**
 * vc_scripts/create_staff.php
 * Script to create a staff/support account via CLI.
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

$email = $argv[1] ?? 'staff@example.com';
$username = $argv[2] ?? 'staff';
$password = $argv[3] ?? 'Staff@123456';
$roleName = $argv[4] ?? 'support'; // Options include: support, sales, finance, technical, manager[cite: 1]

$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
$stmt->execute([$email, $username]);
if ($stmt->fetch()) {
    die("Error: User with this email or username already exists.\n");
}

$passwordHash = password_hash($password, PASSWORD_BCRYPT);
$insert = $pdo->prepare("INSERT INTO users (email, username, password_hash, full_name, status, email_verified_at) VALUES (?, ?, ?, 'Staff Member', 'active', NOW()) RETURNING id");
$insert->execute([$email, $username, $passwordHash]);
$user = $insert->fetch();
$userId = $user['id'];

$roleStmt = $pdo->prepare("SELECT id FROM roles WHERE name = ?");
$roleStmt->execute([$roleName]);
$role = $roleStmt->fetch();

if ($role) {
    $assign = $pdo->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?) ON CONFLICT DO NOTHING");
    $assign->execute([$userId, $role['id']]);
    echo "Staff account created successfully (Username: $username, Email: $email, Role: $roleName).\n";
} else {
    echo "User created, but role '$roleName' was not found in the database.\n";
}
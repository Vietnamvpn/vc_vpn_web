<?php
/**
 * vc_cron/subscription_renewal.php
 * Automatically processes recurring subscription renewals.
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
    $pdo = new PDO("pgsql:host=$dbHost;port=$dbPort;dbname=$dbName", $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // Fetch subscriptions due for auto-renewal
    $stmt = $pdo->query("SELECT id, user_id, package_id FROM subscriptions WHERE auto_renew = true AND status = 'active' AND expires_at <= NOW() + INTERVAL '1 day'");
    $subscriptions = $stmt->fetchAll();

    foreach ($subscriptions as $sub) {
        echo "Processing renewal for subscription ID: {$sub['id']}...\n";
        // Renewal execution and balance deduction logic goes here
    }

    echo "Subscription renewal cron completed.\n";
} catch (PDOException $e) {
    echo "Subscription renewal error: " . $e->getMessage() . "\n";
}
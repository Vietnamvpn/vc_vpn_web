<?php
/**
 * vc_cron/notification_queue.php
 * Processes pending notifications (Email, Telegram, Webhook) from the queue.
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
    $pdo = new PDO("mysql:host=$dbHost;port=$dbPort;dbname=$dbName;charset=utf8mb4", $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $stmt = $pdo->query("SELECT id, recipient, channel, message FROM notification_queues WHERE status = 'pending' LIMIT 50");
    $notifications = $stmt->fetchAll();

    foreach ($notifications as $notification) {
        echo "Sending notification ID {$notification['id']} via {$notification['channel']}...\n";
        // Dispatch logic (Email/Telegram API) goes here

        $update = $pdo->prepare("UPDATE notification_queues SET status = 'sent', sent_at = NOW() WHERE id = ?");
        $update->execute([$notification['id']]);
    }

    echo "Notification queue processed successfully.\n";
} catch (PDOException $e) {
    echo "Notification queue error: " . $e->getMessage() . "\n";
}
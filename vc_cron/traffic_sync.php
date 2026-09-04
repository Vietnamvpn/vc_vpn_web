<?php
/**
 * vc_cron/traffic_sync.php
 * Synchronizes traffic usage data from VPN nodes.
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

    // Fetch active nodes to sync traffic
    $stmt = $pdo->query("SELECT id, name, api_url FROM vpn_nodes WHERE status = 'active'");
    $nodes = $stmt->fetchAll();

    foreach ($nodes as $node) {
        echo "Syncing traffic for node: {$node['name']}...\n";
        // Node API synchronization logic goes here
    }

    echo "Traffic synchronization cron completed.\n";
} catch (PDOException $e) {
    echo "Traffic sync error: " . $e->getMessage() . "\n";
}
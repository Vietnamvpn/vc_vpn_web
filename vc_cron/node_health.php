<?php
/**
 * vc_cron/node_health.php
 * Performs health checks on active VPN nodes.
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

    $stmt = $pdo->query("SELECT id, name, hostname, ip_address FROM vpn_nodes WHERE status != 'disabled'");
    $nodes = $stmt->fetchAll();

    foreach ($nodes as $node) {
        $target = $node['ip_address'] ?? $node['hostname'];
        if (!$target) continue;

        // Simple ping or socket connectivity check
        $startTime = microtime(true);
        $socket = @fsockopen($target, 443, $errno, $errstr, 2);
        $latency = $socket ? round((microtime(true) - $startTime) * 1000) : null;
        if ($socket) fclose($socket);

        $status = $socket ? 'active' : 'offline';

        $logStmt = $pdo->prepare("INSERT INTO node_health_logs (node_id, status, latency_ms, checked_at) VALUES (?, ?, ?, NOW())");
        $logStmt->execute([$node['id'], $status, $latency]);

        $updateStmt = $pdo->prepare("UPDATE vpn_nodes SET status = ?, last_health_at = NOW() WHERE id = ?");
        $updateStmt->execute([$status, $node['id']]);
    }

    echo "Node health checks completed successfully.\n";
} catch (PDOException $e) {
    echo "Node health check error: " . $e->getMessage() . "\n";
}
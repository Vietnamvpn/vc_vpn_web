<?php
/**
 * Tệp cấu hình kết nối Cơ sở dữ liệu (Database Configuration)
 * Ưu tiên bảo mật hàng đầu với PDO.
 */

return [
    'host'     => $_ENV['DB_HOST'] ?? 'localhost',
    'port'     => $_ENV['DB_PORT'] ?? 3306,
    'dbname'   => $_ENV['DB_DATABASE'] ?? $_ENV['DB_NAME'] ?? 'vc_vpn_web_commerce',
    'username' => $_ENV['DB_USERNAME'] ?? $_ENV['DB_USER'] ?? 'root',
    'password' => $_ENV['DB_PASSWORD'] ?? $_ENV['DB_PASS'] ?? '',
    'charset'  => 'utf8mb4',
    'options'  => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false, // Tăng cường bảo mật: tắt giả lập prepare statement để chống SQL Injection
    ]
];
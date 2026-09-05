<?php

namespace VcCore;

class Application
{
    protected static $instance;
    protected $config = [];
    protected $db;
    protected $router;

    public function __construct()
    {
        self::$instance = $this;
        $this->loadConfigurations();
        $this->initDatabase();
        $this->initRouter();
    }

    public static function getInstance()
    {
        return self::$instance;
    }

    protected function loadConfigurations()
    {
        $configDir = __DIR__ . '/../../vc_config/';
        $files = ['app', 'database', 'auth', 'security', 'payment', 'vpn', 'mail'];
        
        foreach ($files as $file) {
            $filePath = $configDir . $file . '.php';
            if (file_exists($filePath)) {
                $this->config[$file] = require $filePath;
            }
        }
    }

    public function getConfig($key, $default = null)
    {
        $keys = explode('.', $key);
        $value = $this->config;

        foreach ($keys as $segment) {
            if (isset($value[$segment])) {
                $value = $value[$segment];
            } else {
                return $default;
            }
        }

        return $value;
    }

    protected function initDatabase()
    {
        $dbConfig = $this->getConfig('database');
        if ($dbConfig) {
            try {
                $dsn = sprintf(
                    'mysql:host=%s;dbname=%s;charset=%s',
                    $dbConfig['host'],
                    $dbConfig['dbname'],
                    $dbConfig['charset']
                );
                $this->db = new \PDO($dsn, $dbConfig['username'], $dbConfig['password'], $dbConfig['options']);
            } catch (\PDOException $e) {
                // Ưu tiên bảo mật tuyệt đối: Không hiển thị chi tiết lỗi database ra ngoài màn hình
                error_log('Database Connection Error: ' . $e->getMessage());
                http_response_code(500);
                exit('Internal Server Error: Database connection failed.');
            }
        }
    }

    public function getDb()
    {
        return $this->db;
    }

    protected function initRouter()
    {
        $this->router = new Router();
    }

    public function getRouter()
    {
        return $this->router;
    }

    public function run()
    {
        // Nạp toàn bộ các tệp định tuyến từ thư mục vc_routes
        $routeDir = __DIR__ . '/../vc_routes/';
        $routeFiles = ['web.php', 'api.php', 'auth.php', 'user.php', 'admin.php', 'staff.php', 'subscription.php'];
        
        foreach ($routeFiles as $file) {
            $routePath = $routeDir . $file;
            if (file_exists($routePath)) {
                require_once $routePath;
            }
        }

        // Lấy URI và HTTP Method hiện tại của request
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        
        // Loại bỏ query string khỏi URI để định tuyến chính xác
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }

        // Chạy bộ định tuyến phân phối request
        $this->router->dispatch($uri, $method);
    }
}
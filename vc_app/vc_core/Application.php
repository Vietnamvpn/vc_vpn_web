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
        $this->loadEnv();
        $this->loadConfigurations();
        $this->initDatabase();
        $this->initRouter();
    }

    public static function getInstance()
    {
        return self::$instance;
    }

    protected function loadEnv()
    {
        $envPath = __DIR__ . '/../../.env';
        if (file_exists($envPath)) {
            $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) {
                    continue;
                }
                if (strpos($line, '=') !== false) {
                    list($name, $value) = explode('=', $line, 2);
                    $name = trim($name);
                    $value = trim(trim($value), '"\'');
                    $_ENV[$name] = $value;
                    \putenv("$name=$value");
                }
            }
        }
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
                    'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                    $dbConfig['host'] ?? '127.0.0.1',
                    $dbConfig['port'] ?? 3306,
                    $dbConfig['dbname'],
                    $dbConfig['charset']
                );
                $this->db = new \PDO($dsn, $dbConfig['username'], $dbConfig['password'], $dbConfig['options']);
            } catch (\PDOException $e) {
                // Hiển thị lỗi chi tiết để debug trực tiếp trên web
                http_response_code(500);
                exit('Database Error Debug: ' . $e->getMessage());
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
        if (!defined('APP_BASE_PATH')) {
            define('APP_BASE_PATH', dirname(__DIR__, 2));
        }

        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        
        if (($pos = strpos($requestUri, '?')) !== false) {
            $requestUri = substr($requestUri, 0, $pos);
        }

        $routeDir = __DIR__ . '/../vc_routes/';
        $routeFiles = ['web.php', 'api.php', 'auth.php', 'user.php', 'admin.php', 'staff.php', 'subscription.php'];
        
        foreach ($routeFiles as $file) {
            $routePath = $routeDir . $file;
            if (file_exists($routePath)) {
                require_once $routePath;
            }
        }

        $uri = $requestUri;
        $method = $requestMethod;

        $this->router->dispatch($uri, $method);
    }
}
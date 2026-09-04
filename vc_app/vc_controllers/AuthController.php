<?php
namespace VcApp\Controllers;

class AuthController
{
    public function showLoginForm()
    {
        $loginFile = APP_BASE_PATH . '/vc_admin/login.php';
        if (file_exists($loginFile)) {
            require_once $loginFile;
        } else {
            echo "Admin login view not found.";
        }
    }

    public function login()
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $_SESSION['error'] = 'Vui lòng nhập đầy đủ tài khoản và mật khẩu.';
            header('Location: /admin/login');
            exit;
        }

        $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
        $db   = $_ENV['DB_DATABASE'] ?? '';
        $user = $_ENV['DB_USERNAME'] ?? '';
        $pass = $_ENV['DB_PASSWORD'] ?? '';
        $port = $_ENV['DB_PORT'] ?? '3306';

        try {
            $pdo = new \PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $user, $pass, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            ]);

            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1");
            $stmt->execute([$username, $username]);
            $userRecord = $stmt->fetch();

            if ($userRecord && password_verify($password, $userRecord['password'])) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_user_id'] = $userRecord['id'];
                $_SESSION['admin_username'] = $userRecord['username'];
                header('Location: /admin/dashboard');
                exit;
            } else {
                $_SESSION['error'] = 'Tài khoản hoặc mật khẩu không chính xác.';
                header('Location: /admin/login');
                exit;
            }
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Lỗi hệ thống cơ sở dữ liệu.';
            header('Location: /admin/login');
            exit;
        }
    }

    public function logout()
    {
        unset($_SESSION['admin_logged_in'], $_SESSION['admin_user_id'], $_SESSION['admin_username']);
        header('Location: /admin/login');
        exit;
    }
}
<?php

namespace VcApp\VcControllers;

use VcCore\Controller;
use VcCore\Application;

class AuthController extends Controller
{
    /**
     * Hiển thị giao diện đăng nhập quản trị / hệ thống
     */
    public function showLoginForm()
    {
        // Nếu đã đăng nhập rồi thì chuyển hướng thẳng vào dashboard hoặc trang chủ
        if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
            $this->redirect('/admin/dashboard');
        }

        $this->view('vc_public/login');
    }

    /**
     * Xử lý dữ liệu đăng nhập
     */
    public function processLogin()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($password)) {
                $this->view('vc_public/login', ['error' => 'Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu.']);
                return;
            }

            $app = Application::getInstance();
            $db = $app->getDb();

            try {
                // Truy vấn bảo mật chống SQL Injection bằng Prepared Statement
                $stmt = $db->prepare("SELECT * FROM vc_admins WHERE username = :username LIMIT 1");
                $stmt->execute(['username' => $username]);
                $admin = $stmt->fetch();

                if ($admin && password_verify($password, $admin['password'])) {
                    // Đăng nhập thành công, chống tấn công Session Fixation
                    session_regenerate_id(true);
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_username'] = $admin['username'];

                    $this->redirect('/admin/dashboard');
                } else {
                    $this->view('vc_public/login', ['error' => 'Tên đăng nhập hoặc mật khẩu không chính xác.']);
                }
            } catch (\PDOException $e) {
                error_log('Login Error: ' . $e->getMessage());
                $this->view('vc_public/login', ['error' => 'Đã xảy ra lỗi hệ thống, vui lòng thử lại sau.']);
            }
        } else {
            $this->redirect('/admin/login');
        }
    }

    /**
     * Xử lý đăng xuất tài khoản
     */
    public function logout()
    {
        // Hủy bỏ toàn bộ session an toàn
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();

        $this->redirect('/admin/login');
    }
}
<?php

namespace VcApp\VcControllers;

use VcCore\Controller;
use VcCore\Application;

class AdminDashboardController extends Controller
{
    /**
     * Hiển thị trang tổng quan quản trị (Admin Dashboard)
     */
    public function index()
    {
        // Kiểm tra quyền đăng nhập quản trị dựa trên session từ AuthController
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            $this->redirect('/admin/login');
            return;
        }

        $app = Application::getInstance();
        $db = $app->getDb();

        // Gọi view trang quản trị hệ thống
        $this->view('vc_admin/dashboard');
    }
}

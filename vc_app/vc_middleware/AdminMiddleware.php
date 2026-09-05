<?php

namespace VcApp\VcMiddleware;

class AdminMiddleware
{
    /**
     * Xử lý kiểm tra quyền truy cập khu vực quản trị (Admin RBAC)
     */
    public function handle()
    {
        // Kiểm tra xem phiên làm việc của admin đã được xác thực chưa
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            // Chưa đăng nhập hoặc không có quyền -> Chuyển hướng về trang đăng nhập
            header('Location: /admin/login');
            exit;
        }

        // Có thể bổ sung kiểm tra phân quyền chi tiết (RBAC) ở đây nếu cần thiết
    }
}
<?php

namespace VcApp\VcMiddleware;

class StaffMiddleware
{
    /**
     * Kiểm tra quyền truy cập của nhân viên (Staff RBAC)
     */
    public function handle()
    {
        if (!isset($_SESSION['staff_logged_in']) || $_SESSION['staff_logged_in'] !== true) {
            header('Location: /staff/login');
            exit;
        }
    }
}
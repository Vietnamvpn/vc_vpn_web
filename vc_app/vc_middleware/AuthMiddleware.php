<?php

namespace VcApp\VcMiddleware;

class AuthMiddleware
{
    /**
     * Kiểm tra người dùng đã đăng nhập hay chưa (User Authentication)
     */
    public function handle()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
    }
}
<?php

namespace VcApp\VcMiddleware;

class GuestMiddleware
{
    /**
     * Chỉ cho phép khách (chưa đăng nhập) truy cập trang login/register
     */
    public function handle()
    {
        if (isset($_SESSION['user_id'])) {
            header('Location: /user/dashboard');
            exit;
        }
    }
}
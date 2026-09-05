<?php

namespace VcApp\VcMiddleware;

class CsrfMiddleware
{
    /**
     * Kiểm tra mã xác thực CSRF Token chống giả mạo yêu cầu chéo trang
     */
    public function handle()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
            
            if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
                http_response_code(403);
                exit('403 Forbidden: CSRF Token không hợp lệ.');
            }
        }
    }
}
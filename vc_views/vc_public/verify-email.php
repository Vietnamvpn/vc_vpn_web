<?php
if (!defined('APP_BASE_PATH')) {
    header("HTTP/1.0 403 Forbidden");
    exit('Direct access forbidden.');
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác Thực Email - VC VPN Web</title>
    <link rel="stylesheet" href="/vc_assets/vc_css/auth.css">
</head>
<body class="auth-page">
    <div class="auth-container text-center">
        <h2>Xác Thực Địa Chỉ Email</h2>
        <?php 
        $alertPath = APP_BASE_PATH . '/vc_views/vc_components/alert.php';
        if (file_exists($alertPath)) require_once $alertPath;
        ?>
        <p>Vui lòng kiểm tra hộp thư đến của bạn để hoàn tất quá trình xác thực tài khoản.</p>
        <form action="/verify-email/resend" method="POST" class="mt-4">
            <button type="submit" class="btn btn-secondary">Gửi Lại Email Xác Thực</button>
        </form>
        <p class="auth-redirect mt-3"><a href="/login">Đăng nhập ngay</a></p>
    </div>
    <script src="/vc_assets/vc_js/auth.js"></script>
</body>
</html>
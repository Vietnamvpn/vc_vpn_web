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
    <title>Quên Mật Khẩu - VC VPN Web</title>
    <link rel="stylesheet" href="/vc_assets/vc_css/auth.css">
</head>
<body class="auth-page">
    <div class="auth-container">
        <h2>Khôi Phục Mật Khẩu</h2>
        <p class="text-muted">Nhập email đăng ký của bạn để nhận liên kết đặt lại mật khẩu.</p>
        <?php 
        $alertPath = APP_BASE_PATH . '/vc_views/vc_components/alert.php';
        if (file_exists($alertPath)) require_once $alertPath;
        ?>
        <form action="/forgot-password" method="POST">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Gửi Yêu Cầu</button>
        </form>
        <p class="auth-redirect"><a href="/login">Quay lại đăng nhập</a></p>
    </div>
    <script src="/vc_assets/vc_js/auth.js"></script>
</body>
</html>
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
    <title>Đăng Nhập - VC VPN Web</title>
    <link rel="stylesheet" href="/vc_assets/vc_css/auth.css">
</head>
<body class="auth-page">
    <div class="auth-container">
        <h2>Đăng Nhập Hệ Thống</h2>
        <?php 
        $alertPath = APP_BASE_PATH . '/vc_views/vc_components/alert.php';
        if (file_exists($alertPath)) require_once $alertPath;
        ?>
        <form action="/login" method="POST">
            <div class="form-group">
                <label>Email đăng nhập</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Mật khẩu</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="form-group remember-forgot">
                <label><input type="checkbox" name="remember"> Nhớ tài khoản</label>
                <a href="/forgot-password">Quên mật khẩu?</a>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Đăng Nhập</button>
        </form>
        <p class="auth-redirect">Chưa có tài khoản? <a href="/register">Đăng ký ngay</a></p>
    </div>
    <script src="/vc_assets/vc_js/auth.js"></script>
</body>
</html>
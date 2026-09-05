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
    <title>Đăng Ký Tài Khoản - VC VPN Web</title>
    <link rel="stylesheet" href="/vc_assets/vc_css/auth.css">
</head>
<body class="auth-page">
    <div class="auth-container">
        <h2>Đăng Ký Tài Khoản</h2>
        <?php 
        $alertPath = APP_BASE_PATH . '/vc_views/vc_components/alert.php';
        if (file_exists($alertPath)) require_once $alertPath;
        ?>
        <form action="/register" method="POST">
            <div class="form-group">
                <label>Tên tài khoản</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Mật khẩu</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Xác nhận mật khẩu</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Đăng Ký</button>
        </form>
        <p class="auth-redirect">Đã có tài khoản? <a href="/login">Đăng nhập</a></p>
    </div>
    <script src="/vc_assets/vc_js/auth.js"></script>
</body>
</html>
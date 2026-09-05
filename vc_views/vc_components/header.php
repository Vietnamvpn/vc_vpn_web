<?php
if (!defined('APP_BASE_PATH')) {
    header("HTTP/1.0 403 Forbidden");
    exit('Direct access forbidden.');
}
?>
<header class="site-header">
    <div class="container">
        <div class="logo">
            <a href="/">🛡️ VC VPN Web</a>
        </div>
        <nav class="main-nav">
            <ul>
                <li><a href="/">Trang Chủ</a></li>
                <li><a href="/pricing">Bảng Giá</a></li>
                <li><a href="/features">Tính Năng</a></li>
                <li><a href="/faq">FAQ</a></li>
                <li><a href="/contact">Liên Hệ</a></li>
            </ul>
        </nav>
        <div class="auth-actions">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="/user/dashboard" class="btn btn-sm">Bảng Điều Khiển</a>
                <a href="/admin/logout" class="btn btn-sm btn-outline">Đăng Xuất</a>
            <?php else: ?>
                <a href="/login" class="btn btn-sm">Đăng Nhập</a>
                <a href="/register" class="btn btn-sm btn-primary">Đăng Ký</a>
            <?php endif; ?>
        </div>
    </div>
</header>
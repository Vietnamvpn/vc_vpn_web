<?php
if (!defined('APP_BASE_PATH')) {
    header("HTTP/1.0 403 Forbidden");
    exit('Direct access forbidden.');
}
?>
<nav class="top-navbar">
    <div class="navbar-left">
        <button id="sidebar-toggle" class="sidebar-toggle-btn">☰</button>
        <span class="navbar-title"><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Dashboard'; ?></span>
    </div>
    <div class="navbar-right">
        <div class="user-dropdown">
            <span class="username"><?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Tài Khoản'; ?></span>
            <div class="dropdown-menu">
                <a href="/user/profile">Hồ Sơ Cá Nhân</a>
                <a href="/user/security">Bảo Mật</a>
                <a href="/admin/logout">Đăng Xuất</a>
            </div>
        </div>
    </div>
</nav>
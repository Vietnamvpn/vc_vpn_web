<?php
if (!defined('APP_BASE_PATH')) {
    header("HTTP/1.0 403 Forbidden");
    exit('Direct access forbidden.');
}
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$isStaff = isset($_SESSION['role']) && $_SESSION['role'] === 'staff';
?>
<aside class="sidebar">
    <div class="sidebar-brand">
        <h2>VC VPN Manager</h2>
    </div>
    <ul class="sidebar-menu">
        <?php if ($isAdmin): ?>
            <li><a href="/admin/dashboard">📊 Tổng Quan Admin</a></li>
            <li><a href="/admin/vc_users">👥 Quản Lý Người Dùng</a></li>
            <li><a href="/admin/vc_plans">📦 Quản Lý Gói Cước</a></li>
            <li><a href="/admin/vc_nodes">🌐 Quản Lý Nodes VPN</a></li>
            <li><a href="/admin/vc_orders">🛒 Quản Lý Đơn Hàng</a></li>
            <li><a href="/admin/vc_payments">💳 Quản Lý Thanh Toán</a></li>
            <li><a href="/admin/vc_subscriptions">⚡ Quản Lý Subscriptions</a></li>
            <li><a href="/admin/vc_settings/general">⚙️ Cài Đặt Hệ Thống</a></li>
        <?php elseif ($isStaff): ?>
            <li><a href="/staff/dashboard">📊 Tổng Quan Staff</a></li>
            <li><a href="/staff/vc_customers">👥 Khách Hàng</a></li>
            <li><a href="/staff/vc_orders">🛒 Đơn Hàng</a></li>
            <li><a href="/staff/vc_support">💬 Hỗ Trợ Khách Hàng</a></li>
        <?php else: ?>
            <li><a href="/user/dashboard">📊 Tổng Quan User</a></li>
            <li><a href="/user/vc_subscriptions">⚡ Gói Cước Của Tôi</a></li>
            <li><a href="/user/vc_orders">🛒 Lịch Sử Đơn Hàng</a></li>
            <li><a href="/user/vc_referral">🎁 Giới Thiệu Bạn Bè</a></li>
            <li><a href="/user/vc_support">💬 Yêu Cầu Hỗ Trợ</a></li>
        <?php endif; ?>
        <li><a href="/admin/logout">🚪 Đăng Xuất</a></li>
    </ul>
</aside>
<?php
/**
 * vc_views/vc_public/home.php
 * Giao diện trang chủ - VC VPN Web
 */

// Ngăn chặn truy cập trực tiếp vào file để đảm bảo bảo mật
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
    <title>VC VPN Web - Hệ Thống VPN Tốc Độ Cao</title>
    <!-- Gắn sẵn đường dẫn CSS theo cấu trúc thư mục -->
    <link rel="stylesheet" href="/vc_assets/vc_css/app.css">
</head>
<body>
    <header>
        <div class="logo">
            <h1>🛡️ VC VPN Web</h1>
        </div>
        <nav>
            <ul>
                <li><a href="/">Trang Chủ</a></li>
                <li><a href="/pricing">Bảng Giá</a></li>
                <li><a href="/login">Đăng Nhập</a></li>
                <li><a href="/register">Đăng Ký</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="hero">
            <h2>Hệ thống quản lý dịch vụ VPN Tối Ưu</h2>
            <p>Tự động hóa cấp phát tài khoản, quản lý gói cước và tích hợp thanh toán dễ dàng.</p>
            <a href="/pricing" class="btn">Xem Gói Cước</a>
        </section>

        <section class="features">
            <h3>Tính Năng Nổi Bật</h3>
            <ul>
                <li>Đồng bộ lưu lượng tự động</li>
                <li>Bảo mật dữ liệu tuyệt đối</li>
                <li>Hỗ trợ đa nền tảng (Sing-box, Xray)</li>
            </ul>
        </section>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> VC VPN Web. All rights reserved.</p>
    </footer>

    <!-- Gắn sẵn đường dẫn JS theo cấu trúc thư mục -->
    <script src="/vc_assets/vc_js/app.js"></script>
</body>
</html>
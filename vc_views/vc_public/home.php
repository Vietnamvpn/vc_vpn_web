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
                <a href="/login" class="btn btn-sm btn-outline">Đăng Nhập</a>
                <a href="/register" class="btn btn-sm btn-primary">Đăng Ký</a>
            </div>
        </div>
    </header>

    <main class="container">
        <section class="hero">
            <h2>Hệ thống quản lý dịch vụ VPN Tối Ưu</h2>
            <p>Tự động hóa cấp phát tài khoản, quản lý gói cước và tích hợp thanh toán dễ dàng.</p>
            <a href="/pricing" class="btn">Xem Gói Cước</a>
        </section>

        <section class="py-5">
            <h3 class="text-center mb-5">Tính Năng Nổi Bật</h3>
            <div class="features-grid">
                <div class="feature-item">
                    <h3>⚡ Đồng bộ lưu lượng tự động</h3>
                    <p>Hệ thống theo dõi và đồng bộ dữ liệu sử dụng băng thông của người dùng theo thời gian thực.</p>
                </div>
                <div class="feature-item">
                    <h3>🛡️ Bảo mật tuyệt đối</h3>
                    <p>Mã hóa dữ liệu chuẩn cao, tích hợp tường lửa và cơ chế chống tấn công toàn diện.</p>
                </div>
                <div class="feature-item">
                    <h3>🌐 Đa nền tảng</h3>
                    <p>Hỗ trợ cấu hình mượt mà trên các ứng dụng tiên tiến như Sing-box và Xray.</p>
                </div>
            </div>
        </section>
    </main>

    <footer class="site-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-col">
                    <h3>VC VPN Web</h3>
                    <p>Hệ thống quản lý dịch vụ VPN, tự động hóa cấp phát tài khoản, quản lý gói cước và tích hợp thanh toán.</p>
                </div>
                <div class="footer-col">
                    <h4>Liên Kết Nhanh</h4>
                    <ul>
                        <li><a href="/">Trang Chủ</a></li>
                        <li><a href="/pricing">Bảng Giá Gói Cước</a></li>
                        <li><a href="/features">Tính Năng Hệ Thống</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Hỗ Trợ</h4>
                    <ul>
                        <li><a href="/contact">Liên Hệ Hỗ Trợ</a></li>
                        <li><a href="/faq">Câu Hỏi Thường Gặp</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> VC VPN Web. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Gắn sẵn đường dẫn JS theo cấu trúc thư mục -->
    <script src="/vc_assets/vc_js/app.js"></script>
</body>
</html>
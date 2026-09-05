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
    <title>Tính Năng Hệ Thống - VC VPN Web</title>
    <link rel="stylesheet" href="/vc_assets/vc_css/app.css">
</head>
<body>
    <?php 
    $headerPath = APP_BASE_PATH . '/vc_views/vc_components/header.php';
    if (file_exists($headerPath)) require_once $headerPath;
    ?>

    <main class="container py-5">
        <section class="text-center mb-5">
            <h1>Tính Năng Nổi Bật Của Hệ Thống</h1>
            <p>Giải pháp quản lý và vận hành dịch vụ VPN toàn diện, tự động hóa tối đa.</p>
        </section>

        <div class="features-grid">
            <div class="feature-item">
                <h3>⚡ Tự Động Hóa Cấp Phát</h3>
                <p>Tài khoản và node được tự động khởi tạo ngay sau khi thanh toán thành công qua API.</p>
            </div>
            <div class="feature-item">
                <h3>🔄 Đồng Bộ Lưu Lượng</h3>
                <p>Hệ thống theo dõi và đồng bộ dữ liệu sử dụng băng thông của người dùng theo thời gian thực.</p>
            </div>
            <div class="feature-item">
                <h3>🛡️ Bảo Mật Tối Ưu</h3>
                <p>Mã hóa dữ liệu chuẩn cao, tích hợp tường lửa và cơ chế chống tấn công toàn diện.</p>
            </div>
            <div class="feature-item">
                <h3>💳 Tích Hợp Thanh Toán</h3>
                <p>Hỗ trợ đa dạng cổng thanh toán tự động, cập nhật trạng thái hóa đơn tức thì.</p>
            </div>
        </div>
    </main>

    <?php 
    $footerPath = APP_BASE_PATH . '/vc_views/vc_components/footer.php';
    if (file_exists($footerPath)) require_once $footerPath;
    ?>
    <script src="/vc_assets/vc_js/app.js"></script>
</body>
</html>
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
    <title>Bảng Giá Gói Cước - VC VPN Web</title>
    <link rel="stylesheet" href="/vc_assets/vc_css/app.css">
</head>
<body>
    <?php 
    $headerPath = APP_BASE_PATH . '/vc_views/vc_components/header.php';
    if (file_exists($headerPath)) require_once $headerPath;
    ?>

    <main class="container py-5">
        <section class="pricing-header text-center mb-5">
            <h1>Bảng Giá Gói Cước VPN</h1>
            <p>Lựa chọn gói dịch vụ tốc độ cao phù hợp với nhu cầu sử dụng của bạn.</p>
        </section>

        <div class="pricing-grid">
            <div class="pricing-card">
                <h3>Gói Cơ Bản</h3>
                <div class="price">50.000đ<span> / tháng</span></div>
                <ul>
                    <li>Tốc độ cao không giới hạn</li>
                    <li>Dung lượng: 100GB / tháng</li>
                    <li>Hỗ trợ 2 thiết bị cùng lúc</li>
                    <li>Mở khóa Node tiêu chuẩn</li>
                </ul>
                <a href="/register" class="btn btn-primary">Đăng Ký Ngay</a>
            </div>

            <div class="pricing-card popular">
                <div class="badge">Phổ Biến</div>
                <h3>Gói Nâng Cao</h3>
                <div class="price">100.000đ<span> / tháng</span></div>
                <ul>
                    <li>Tốc độ cao không giới hạn</li>
                    <li>Dung lượng: 300GB / tháng</li>
                    <li>Hỗ trợ 5 thiết bị cùng lúc</li>
                    <li>Mở khóa toàn bộ Node VIP</li>
                </ul>
                <a href="/register" class="btn btn-primary">Đăng Ký Ngay</a>
            </div>

            <div class="pricing-card">
                <h3>Gói Doanh Nghiệp</h3>
                <div class="price">250.000đ<span> / tháng</span></div>
                <ul>
                    <li>Tốc độ cao ưu tiên</li>
                    <li>Dung lượng: Không giới hạn</li>
                    <li>Hỗ trợ thiết bị không giới hạn</li>
                    <li>Hỗ trợ kỹ thuật riêng 24/7</li>
                </ul>
                <a href="/register" class="btn btn-primary">Đăng Ký Ngay</a>
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
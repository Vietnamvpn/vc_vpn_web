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
    <title>Câu Hỏi Thường Gặp - VC VPN Web</title>
    <link rel="stylesheet" href="/vc_assets/vc_css/app.css">
</head>
<body>
    <?php 
    $headerPath = APP_BASE_PATH . '/vc_views/vc_components/header.php';
    if (file_exists($headerPath)) require_once $headerPath;
    ?>

    <main class="container py-5">
        <h1 class="text-center mb-5">Câu Hỏi Thường Gặp (FAQ)</h1>
        
        <div class="faq-list">
            <div class="faq-item">
                <h3>1. Sau khi thanh toán bao lâu thì nhận được tài khoản?</h3>
                <p>Hệ thống tự động kích hoạt dịch vụ ngay lập tức trong vòng 1-3 phút sau khi giao dịch thanh toán được xác nhận.</p>
            </div>
            <div class="faq-item">
                <h3>2. Tôi có thể sử dụng trên bao nhiêu thiết bị?</h3>
                <p>Tùy thuộc vào gói cước bạn đăng ký (Cơ bản hỗ trợ 2 thiết bị, Nâng cao hỗ trợ 5 thiết bị hoặc không giới hạn).</p>
            </div>
            <div class="faq-item">
                <h3>3. Làm thế nào để cấu hình kết nối VPN?</h3>
                <p>Sau khi đăng nhập, hệ thống cung cấp sẵn đường dẫn cấu hình (Subscription Link) tương thích với các ứng dụng như Sing-box, Xray, Clash.</p>
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
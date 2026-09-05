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
    <title>Liên Hệ Hỗ Trợ - VC VPN Web</title>
    <link rel="stylesheet" href="/vc_assets/vc_css/app.css">
</head>
<body>
    <?php 
    $headerPath = APP_BASE_PATH . '/vc_views/vc_components/header.php';
    if (file_exists($headerPath)) require_once $headerPath;
    ?>

    <main class="container py-5">
        <h1 class="text-center mb-5">Liên Hệ Với Chúng Tôi</h1>
        
        <div class="contact-wrapper">
            <div class="contact-info">
                <h3>Thông Tin Hỗ Trợ</h3>
                <p>Nếu bạn gặp khó khăn trong quá trình sử dụng, vui lòng gửi yêu cầu hỗ trợ qua hệ thống Ticket hoặc liên hệ trực tiếp.</p>
                <p><strong>Email:</strong> support@vpn2s.linksub24h.com</p>
                <p><strong>Hỗ trợ trực tuyến:</strong> 24/7 qua hệ thống</p>
            </div>
            <form class="contact-form" method="POST" action="/contact/send">
                <div class="form-group">
                    <label>Họ và Tên</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Email của bạn</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Nội dung cần hỗ trợ</label>
                    <textarea name="message" class="form-control" rows="5" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Gửi Yêu Cầu</button>
            </form>
        </div>
    </main>

    <?php 
    $footerPath = APP_BASE_PATH . '/vc_views/vc_components/footer.php';
    if (file_exists($footerPath)) require_once $footerPath;
    ?>
    <script src="/vc_assets/vc_js/app.js"></script>
</body>
</html>
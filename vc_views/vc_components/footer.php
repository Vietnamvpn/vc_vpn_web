<?php
if (!defined('APP_BASE_PATH')) {
    header("HTTP/1.0 403 Forbidden");
    exit('Direct access forbidden.');
}
?>
<footer class="site-footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-col">
                <h3>VC VPN Web</h3>
                <p>Hệ thống quản lý dịch vụ VPN, tự động hóa cấp phát tài khoản, quản lý gói cước, đồng bộ lưu lượng và tích hợp thanh toán.</p>
            </div>
            <div class="footer-col">
                <h4>Liên Kết Nhanh</h4>
                <ul>
                    <li><a href="/">Trang Chủ</a></li>
                    <li><a href="/pricing">Bảng Giá Gói Cước</a></li>
                    <li><a href="/features">Tính Năng Hệ Thống</a></li>
                    <li><a href="/faq">Câu Hỏi Thường Gặp</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Hỗ Trợ & Pháp Lý</h4>
                <ul>
                    <li><a href="/contact">Liên Hệ Hỗ Trợ</a></li>
                    <li><a href="/terms">Điều Khoản Sử Dụng</a></li>
                    <li><a href="/privacy">Chính Sách Bảo Mật</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> VC VPN Web. All rights reserved.</p>
        </div>
    </div>
</footer>
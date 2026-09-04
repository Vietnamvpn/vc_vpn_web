# 🛡️ VC VPN Web - System Management & Commerce

Hệ thống quản lý dịch vụ VPN, tự động hóa cấp phát tài khoản, quản lý gói cước, đồng bộ lưu lượng và tích hợp thanh toán.

---

## 🚀 Hướng Dẫn Cài Đặt Theo Thứ Tự (Step-by-Step)

Thực hiện lần lượt các bước dưới đây trên VPS / Server của bạn:

### Bước 1: Di chuyển vào thư mục cài đặt
* **Mục đích:** Truy cập đúng thư mục gốc của trang web trên Server (thay đổi đường dẫn theo đúng tên miền của bạn nếu cần).

```bash
cd /www/wwwroot/vpn2s.linksub24h.com
```

### Bước 2: Tải mã nguồn từ GitHub
* **Mục đích:** Clone toàn bộ mã nguồn của dự án về thư mục hiện tại.

```bash
git clone https://github.com/Vietnamvpn/vc_vpn_web.git .
```

### Bước 3: Phân quyền file cài đặt
* **Mục đích:** Cấp quyền thực thi (`+x`) cho file script `vc_install.sh` để có thể chạy trực tiếp.

```bash
chmod +x vc_install.sh
```

### Bước 4: Chạy Script cài đặt tự động
* **Mục đích:** Khởi chạy quá trình tự động thiết lập hệ thống, cơ sở dữ liệu và cấu hình ban đầu.

```bash
./vc_install.sh
```

---

## 🌐 Cấu Hình Web Server & Điều Hướng (Apache / `.htaccess`)

* **Mục đích:** Cấu hình tệp `.htaccess` tại thư mục gốc của dự án (`vc_public/` hoặc thư mục web root) để chặn duyệt thư mục trái phép và chuyển hướng tất cả Request về tệp `index.php` phục vụ cơ chế Router.

Tạo hoặc chỉnh sửa tệp `.htaccess` với nội dung sau:

```apache
<IfModule mod_rewrite.c>
    Options -MultiViews -Indexes
    RewriteEngine On

    # Xử lý chuyển hướng nếu không phải là tệp hoặc thư mục thực tế
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^ index.php [L]
</IfModule>
```

* **Giải thích chi tiết:**
  * `Options -MultiViews -Indexes`: Ẩn danh sách tệp/thư mục khi không có file chỉ mục (`index`), ngăn ngừa lộ tài nguyên.
  * `RewriteEngine On`: Bật bộ máy điều hướng URL của Apache.
  * `RewriteCond %{REQUEST_FILENAME} !-f`: Kiểm tra nếu đường dẫn KHÔNG trỏ tới một tệp tin thực tế.
  * `RewriteCond %{REQUEST_FILENAME} !-d`: Kiểm tra nếu đường dẫn KHÔNG trỏ tới một thư mục thực tế.
  * `RewriteRule ^ index.php [L]`: Chuyển hướng toàn bộ yêu cầu còn lại vào file `index.php` làm lối vào duy nhất (Single Entry Point).

---

## ⚙️ Cấu Hình Môi Trường & Bảo Mật PHP

### 1. Bật các PHP Extensions bắt buộc
* **Mục đích:** Đảm bảo PHP có đủ các thư viện phụ thuộc để vận hành toàn bộ chức năng.

* **`pdo_mysql`**: Kết nối và làm việc với cơ sở dữ liệu MySQL / MariaDB.
* **`openssl`**: Mã hóa dữ liệu, mã hóa token và thông tin đăng nhập.
* **`mbstring`**: Xử lý chuỗi văn bản đa ngôn ngữ (chuẩn UTF-8).
* **`curl`**: Gửi yêu cầu API đến các Node VPN và cổng thanh toán.
* **`json`**: Đọc/ghi file cấu hình JSON và dữ liệu JSONB.
* **`fileinfo`**: Kiểm tra định dạng an toàn của tệp tải lên (Avatar, chứng từ).

### 2. Cấu hình bảo mật trong file `php.ini`
* **Mục đích:** Vô hiệu hóa các hàm nguy hiểm và ẩn thông tin hệ thống khỏi hacker.

* **Tắt các hàm nguy hiểm:**
  ```ini
  disable_functions = exec, system, passthru, shell_exec, proc_open, popen
  ```
* **Tắt hiển thị lỗi trực tiếp (tránh lộ thông tin nguồn khi xảy ra sự cố):**
  ```ini
  display_errors = Off
  ```
* **Ẩn phiên bản PHP trên Header:**
  ```ini
  expose_php = Off
  ```

---

## 📂 Cấu Trúc Thư Mục Dự Án

```text
vc_vpn_web/
│
├── vc_install.sh
├── vc_update.sh
├── composer.json
├── .env.example
├── .gitignore
├── .htaccess
├── README.md
│
├── vc_config/
│   ├── app.php
│   ├── database.php
│   ├── auth.php
│   ├── security.php
│   ├── payment.php
│   ├── vpn.php
│   └── mail.php
│
├── vc_database/
│   ├── vc_vpn_web_commerce.sql
│   ├── vc_migrations/
│   │   ├── 001_initial_schema.sql
│   │   ├── 002_indexes.sql
│   │   └── 003_seed_permissions.sql
│   │
│   └── vc_seeds/
│       ├── roles.php
│       ├── permissions.php
│       ├── settings.php
│       └── plans.php
│
├── vc_public/
│   ├── index.php
│   ├── .htaccess
│   │
│   ├── vc_assets/
│   │   ├── vc_css/
│   │   │   ├── app.css
│   │   │   ├── auth.css
│   │   │   ├── user.css
│   │   │   ├── admin.css
│   │   │   └── staff.css
│   │   │
│   │   ├── vc_js/
│   │   │   ├── app.js
│   │   │   ├── auth.js
│   │   │   ├── user.js
│   │   │   ├── admin.js
│   │   │   └── staff.js
│   │   │
│   │   ├── vc_images/
│   │   │   ├── logo.svg
│   │   │   ├── favicon.ico
│   │   │   └── placeholder.svg
│   │   │
│   │   └── vc_fonts/
│   │
│   └── vc_uploads/
│       ├── avatars/
│       ├── invoices/
│       └── attachments/
│
├── vc_app/
│   │
│   ├── vc_core/
│   │   ├── Application.php
│   │   ├── Controller.php
│   │   ├── Model.php
│   │   ├── Repository.php
│   │   ├── Request.php
│   │   ├── Response.php
│   │   ├── Router.php
│   │   ├── Session.php
│   │   └── View.php
│   │
│   ├── vc_controllers/
│   │   ├── AuthController.php
│   │   ├── UserController.php
│   │   ├── PlanController.php
│   │   ├── OrderController.php
│   │   ├── PaymentController.php
│   │   ├── InvoiceController.php
│   │   ├── SubscriptionController.php
│   │   ├── DeviceController.php
│   │   ├── NodeController.php
│   │   ├── TrafficController.php
│   │   ├── CouponController.php
│   │   ├── ReferralController.php
│   │   ├── SupportController.php
│   │   ├── NotificationController.php
│   │   └── SettingsController.php
│   │
│   ├── vc_models/
│   │   ├── User.php
│   │   ├── UserProfile.php
│   │   ├── UserAddress.php
│   │   ├── Role.php
│   │   ├── Permission.php
│   │   ├── VpnPlan.php
│   │   ├── PlanPrice.php
│   │   ├── PlanFeature.php
│   │   ├── NodeGroup.php
│   │   ├── VpnNode.php
│   │   ├── Order.php
│   │   ├── OrderItem.php
│   │   ├── Payment.php
│   │   ├── Invoice.php
│   │   ├── Coupon.php
│   │   ├── CouponUsage.php
│   │   ├── Subscription.php
│   │   ├── SubscriptionToken.php
│   │   ├── SubscriptionAccess.php
│   │   ├── SubscriptionEvent.php
│   │   ├── SubscriptionTraffic.php
│   │   ├── UserDevice.php
│   │   ├── SubscriptionDevice.php
│   │   ├── ReferralCode.php
│   │   ├── Referral.php
│   │   ├── Commission.php
│   │   ├── SupportTicket.php
│   │   ├── SupportMessage.php
│   │   ├── Notification.php
│   │   ├── Announcement.php
│   │   ├── SystemSetting.php
│   │   ├── AuditLog.php
│   │   └── WebhookLog.php
│   │
│   ├── vc_repositories/
│   │   ├── UserRepository.php
│   │   ├── RoleRepository.php
│   │   ├── PermissionRepository.php
│   │   ├── PlanRepository.php
│   │   ├── NodeRepository.php
│   │   ├── OrderRepository.php
│   │   ├── PaymentRepository.php
│   │   ├── InvoiceRepository.php
│   │   ├── CouponRepository.php
│   │   ├── SubscriptionRepository.php
│   │   ├── SubscriptionTokenRepository.php
│   │   ├── SubscriptionAccessRepository.php
│   │   ├── TrafficRepository.php
│   │   ├── DeviceRepository.php
│   │   ├── ReferralRepository.php
│   │   ├── CommissionRepository.php
│   │   ├── TicketRepository.php
│   │   ├── NotificationRepository.php
│   │   ├── SettingsRepository.php
│   │   └── AuditRepository.php
│   │
│   ├── vc_services/
│   │   │
│   │   ├── vc_auth/
│   │   │   ├── LoginService.php
│   │   │   ├── RegisterService.php
│   │   │   ├── LogoutService.php
│   │   │   ├── PasswordService.php
│   │   │   ├── EmailVerificationService.php
│   │   │   └── TwoFactorService.php
│   │   │
│   │   ├── vc_users/
│   │   │   ├── UserService.php
│   │   │   ├── ProfileService.php
│   │   │   ├── AddressService.php
│   │   │   └── DeviceService.php
│   │   │
│   │   ├── vc_products/
│   │   │   ├── PlanService.php
│   │   │   ├── PriceService.php
│   │   │   └── FeatureService.php
│   │   │
│   │   ├── vc_orders/
│   │   │   ├── CartService.php
│   │   │   ├── OrderService.php
│   │   │   ├── OrderItemService.php
│   │   │   └── InvoiceService.php
│   │   │
│   │   ├── vc_payments/
│   │   │   ├── PaymentService.php
│   │   │   ├── PaymentVerificationService.php
│   │   │   ├── WebhookService.php
│   │   │   └── RefundService.php
│   │   │
│   │   ├── vc_subscriptions/
│   │   │   ├── SubscriptionService.php
│   │   │   ├── SubscriptionCreateService.php
│   │   │   ├── SubscriptionTokenService.php
│   │   │   ├── SubscriptionAccessService.php
│   │   │   ├── SubscriptionRenewalService.php
│   │   │   ├── SubscriptionSuspendService.php
│   │   │   ├── SubscriptionCancelService.php
│   │   │   ├── SubscriptionTrafficService.php
│   │   │   └── SubscriptionDeviceService.php
│   │   │
│   │   ├── vc_vpn/
│   │   │   ├── NodeService.php
│   │   │   ├── NodeGroupService.php
│   │   │   ├── NodeHealthService.php
│   │   │   ├── ProvisioningService.php
│   │   │   ├── ConfigGeneratorService.php
│   │   │   └── TrafficSyncService.php
│   │   │
│   │   ├── vc_affiliate/
│   │   │   ├── ReferralService.php
│   │   │   └── CommissionService.php
│   │   │
│   │   ├── vc_support/
│   │   │   ├── TicketService.php
│   │   │   └── MessageService.php
│   │   │
│   │   ├── vc_notifications/
│   │   │   ├── NotificationService.php
│   │   │   ├── EmailNotificationService.php
│   │   │   └── AnnouncementService.php
│   │   │
│   │   └── vc_admin/
│   │       ├── AdminDashboardService.php
│   │       ├── StaffService.php
│   │       ├── RoleService.php
│   │       ├── PermissionService.php
│   │       ├── SettingsService.php
│   │       └── AuditService.php
│   │
│   ├── vc_middleware/
│   │   ├── AuthMiddleware.php
│   │   ├── GuestMiddleware.php
│   │   ├── AdminMiddleware.php
│   │   ├── StaffMiddleware.php
│   │   ├── PermissionMiddleware.php
│   │   ├── CsrfMiddleware.php
│   │   └── RateLimitMiddleware.php
│   │
│   ├── vc_helpers/
│   │   ├── Auth.php
│   │   ├── Security.php
│   │   ├── Password.php
│   │   ├── Token.php
│   │   ├── Validator.php
│   │   ├── Url.php
│   │   ├── Money.php
│   │   ├── Date.php
│   │   ├── Response.php
│   │   └── Logger.php
│   │
│   └── vc_routes/
│       ├── web.php
│       ├── api.php
│       ├── auth.php
│       ├── user.php
│       ├── admin.php
│       ├── staff.php
│       └── subscription.php
│
├── vc_views/
│   │
│   ├── vc_layouts/
│   │   ├── main.php
│   │   ├── auth.php
│   │   ├── user.php
│   │   ├── admin.php
│   │   └── staff.php
│   │
│   ├── vc_components/
│   │   ├── header.php
│   │   ├── footer.php
│   │   ├── navbar.php
│   │   ├── sidebar.php
│   │   ├── alert.php
│   │   ├── modal.php
│   │   ├── pagination.php
│   │   └── table.php
│   │
│   ├── vc_public/
│   │   ├── home.php
│   │   ├── pricing.php
│   │   ├── features.php
│   │   ├── faq.php
│   │   ├── contact.php
│   │   ├── login.php
│   │   ├── register.php
│   │   ├── forgot-password.php
│   │   ├── reset-password.php
│   │   └── verify-email.php
│   │
│   ├── vc_user/
│   │   ├── dashboard.php
│   │   ├── profile.php
│   │   ├── security.php
│   │   ├── devices.php
│   │   ├── notifications.php
│   │   │
│   │   ├── vc_orders/
│   │   │   ├── index.php
│   │   │   └── detail.php
│   │   │
│   │   ├── vc_subscriptions/
│   │   │   ├── index.php
│   │   │   ├── detail.php
│   │   │   ├── token.php
│   │   │   ├── qr.php
│   │   │   ├── traffic.php
│   │   │   └── devices.php
│   │   │
│   │   ├── vc_referral/
│   │   │   ├── index.php
│   │   │   └── commissions.php
│   │   │
│   │   └── vc_support/
│   │       ├── index.php
│   │       ├── create.php
│   │       └── detail.php
│   │
│   ├── vc_admin/
│   │   ├── dashboard.php
│   │   │
│   │   ├── vc_users/
│   │   │   ├── index.php
│   │   │   ├── create.php
│   │   │   ├── edit.php
│   │   │   ├── detail.php
│   │   │   ├── subscriptions.php
│   │   │   └── devices.php
│   │   │
│   │   ├── vc_staff/
│   │   │   ├── index.php
│   │   │   ├── create.php
│   │   │   ├── edit.php
│   │   │   └── detail.php
│   │   │
│   │   ├── vc_roles/
│   │   │   ├── index.php
│   │   │   ├── create.php
│   │   │   └── edit.php
│   │   │
│   │   ├── vc_permissions/
│   │   │   └── index.php
│   │   │
│   │   ├── vc_plans/
│   │   │   ├── index.php
│   │   │   ├── create.php
│   │   │   ├── edit.php
│   │   │   └── detail.php
│   │   │
│   │   ├── vc_prices/
│   │   │   ├── index.php
│   │   │   ├── create.php
│   │   │   └── edit.php
│   │   │
│   │   ├── vc_nodes/
│   │   │   ├── index.php
│   │   │   ├── create.php
│   │   │   ├── edit.php
│   │   │   ├── detail.php
│   │   │   └── health.php
│   │   │
│   │   ├── vc_node_groups/
│   │   │   ├── index.php
│   │   │   ├── create.php
│   │   │   └── edit.php
│   │   │
│   │   ├── vc_orders/
│   │   │   ├── index.php
│   │   │   └── detail.php
│   │   │
│   │   ├── vc_payments/
│   │   │   ├── index.php
│   │   │   ├── detail.php
│   │   │   └── refunds.php
│   │   │
│   │   ├── vc_invoices/
│   │   │   ├── index.php
│   │   │   ├── detail.php
│   │   │   └── pdf.php
│   │   │
│   │   ├── vc_subscriptions/
│   │   │   ├── index.php
│   │   │   ├── create.php
│   │   │   ├── detail.php
│   │   │   ├── edit.php
│   │   │   ├── suspend.php
│   │   │   ├── renew.php
│   │   │   ├── token.php
│   │   │   └── traffic.php
│   │   │
│   │   ├── vc_traffic/
│   │   │   ├── index.php
│   │   │   ├── users.php
│   │   │   ├── nodes.php
│   │   │   └── subscriptions.php
│   │   │
│   │   ├── vc_coupons/
│   │   │   ├── index.php
│   │   │   ├── create.php
│   │   │   ├── edit.php
│   │   │   └── usages.php
│   │   │
│   │   ├── vc_affiliate/
│   │   │   ├── index.php
│   │   │   ├── referrals.php
│   │   │   └── commissions.php
│   │   │
│   │   ├── vc_support/
│   │   │   ├── index.php
│   │   │   └── detail.php
│   │   │
│   │   ├── vc_announcements/
│   │   │   ├── index.php
│   │   │   ├── create.php
│   │   │   └── edit.php
│   │   │
│   │   ├── vc_audit_logs/
│   │   │   ├── index.php
│   │   │   └── detail.php
│   │   │
│   │   └── vc_settings/
│   │       ├── general.php
│   │       ├── payment.php
│   │       ├── vpn.php
│   │       ├── email.php
│   │       └── security.php
│   │
│   └── vc_staff/
│       ├── dashboard.php
│       ├── vc_customers/
│       │   ├── index.php
│       │   └── detail.php
│       ├── vc_orders/
│       │   ├── index.php
│       │   └── detail.php
│       ├── vc_subscriptions/
│       │   ├── index.php
│       │   └── detail.php
│       ├── vc_payments/
│       │   └── index.php
│       ├── vc_support/
│       │   ├── index.php
│       │   └── detail.php
│       ├── vc_nodes/
│       │   └── index.php
│       └── vc_traffic/
│           └── index.php
│
├── vc_admin/
│   ├── index.php
│   ├── login.php
│   ├── logout.php
│   └── dashboard.php
│
├── vc_staff/
│   ├── index.php
│   ├── login.php
│   ├── logout.php
│   └── dashboard.php
│
├── vc_api/
│   ├── index.php
│   │
│   ├── vc_auth/
│   │   ├── login.php
│   │   ├── register.php
│   │   ├── logout.php
│   │   └── refresh.php
│   │
│   ├── vc_users/
│   │   ├── profile.php
│   │   ├── devices.php
│   │   └── notifications.php
│   │
│   ├── vc_plans/
│   │   ├── index.php
│   │   └── detail.php
│   │
│   ├── vc_orders/
│   │   ├── create.php
│   │   ├── index.php
│   │   └── detail.php
│   │
│   ├── vc_payments/
│   │   ├── create.php
│   │   ├── status.php
│   │   └── webhook.php
│   │
│   ├── vc_subscriptions/
│   │   ├── index.php
│   │   ├── detail.php
│   │   ├── renew.php
│   │   └── devices.php
│   │
│   └── vc_support/
│       ├── tickets.php
│       ├── create.php
│       └── messages.php
│
├── vc_subscription/
│   ├── index.php
│   ├── sub.php
│   ├── qr.php
│   ├── config.php
│   ├── clash.php
│   ├── singbox.php
│   └── xray.php
│
├── vc_integrations/
│   │
│   ├── vc_vpn/
│   │   ├── VpnProviderInterface.php
│   │   ├── SingBoxProvider.php
│   │   ├── XrayProvider.php
│   │   ├── WireGuardProvider.php
│   │   └── VpnProviderFactory.php
│   │
│   ├── vc_payment/
│   │   ├── PaymentProviderInterface.php
│   │   ├── StripeProvider.php
│   │   ├── PayPalProvider.php
│   │   └── CryptoProvider.php
│   │
│   └── vc_mail/
│       ├── MailProviderInterface.php
│       └── SmtpProvider.php
│
├── vc_cron/
│   ├── subscription_expiry.php
│   ├── subscription_renewal.php
│   ├── traffic_sync.php
│   ├── node_health.php
│   ├── payment_check.php
│   ├── notification_queue.php
│   ├── cleanup.php
│   └── backup.php
│
├── vc_scripts/
│   ├── install.php
│   ├── migrate.php
│   ├── seed.php
│   ├── create_admin.php
│   ├── create_staff.php
│   ├── backup.php
│   ├── restore.php
│   ├── health_check.php
│   └── clear_cache.php
│
├── vc_storage/
│   ├── vc_cache/
│   ├── vc_sessions/
│   ├── vc_invoices/
│   ├── vc_exports/
│   └── vc_temp/
│
├── vc_logs/
│   ├── vc_app/
│   ├── vc_payment/
│   ├── vc_vpn/
│   ├── vc_security/
│   └── vc_cron/
│
└── vc_docs/
    ├── database.md
    ├── installation.md
    ├── aaPanel.md
    ├── git-deployment.md
    ├── admin.md
    ├── staff.md
    ├── api.md
    ├── subscription.md
    ├── vpn-integration.md
    ├── payment-integration.md
    └── permissions.md
```
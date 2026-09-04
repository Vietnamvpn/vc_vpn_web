#!/bin/bash
# vc_install.sh

set -Eeuo pipefail

APP_PATH="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)"

echo "================================================="
echo " Bắt đầu cài đặt tự động vc_vpn_web lên aaPanel"
echo " Đường dẫn hiện tại: $APP_PATH"
echo "================================================="

# 1. Thu thập thông tin
read -r -p "Nhập tên miền (Domain - ví dụ: vpn.domain.com): " DOMAIN
read -r -p "Nhập tên Database (MySQL): " DB_NAME
read -r -p "Nhập User Database: " DB_USER
read -r -s -p "Nhập Mật khẩu Database: " DB_PASS
printf '\n'

if [[ ! "$DB_NAME" =~ ^[a-zA-Z_][a-zA-Z0-9_]*$ || ! "$DB_USER" =~ ^[a-zA-Z_][a-zA-Z0-9_]*$ ]]; then
    echo "Lỗi: tên database và user chỉ được chứa chữ cái, số và dấu gạch dưới."
    exit 1
fi

if [[ -z "$DOMAIN" || ! "$DOMAIN" =~ ^[a-zA-Z0-9.-]+$ ]]; then
    echo "Lỗi: domain không hợp lệ."
    exit 1
fi

if [[ -z "$DB_PASS" ]]; then
    echo "Lỗi: mật khẩu database không được để trống."
    exit 1
fi

if [[ ! -f "$APP_PATH/.env.example" || ! -f "$APP_PATH/vc_database/vc_vpn_web_commerce_mysql.sql" ]]; then
    echo "Lỗi: thiếu .env.example hoặc file schema MySQL."
    exit 1
fi

escape_sed_replacement() {
    local value="$1"
    value=${value//\\/\\\\}
    value=${value//&/\\&}
    value=${value//|/\\|}
    printf '%s' "$value"
}

# 2. Kiểm tra môi trường MySQL/PHP
if ! command -v mysql &> /dev/null || ! command -v php &> /dev/null; then
    echo "Lỗi: MySQL hoặc PHP chưa được cài đặt trong aaPanel."
    exit 1
fi

if ! php -m | grep -qi '^pdo_mysql$'; then
    echo "Lỗi: PHP chưa bật extension pdo_mysql."
    exit 1
fi

if ! id www >/dev/null 2>&1; then
    echo "Lỗi: không tìm thấy user www của aaPanel."
    exit 1
fi

if [[ "$(id -u)" -ne 0 ]]; then
    echo "Lỗi: hãy chạy script bằng root: sudo ./vc_install.sh"
    exit 1
fi

# 3. Tạo Database và User
echo "Khởi tạo Database và User..."
escape_mysql_literal() {
    local value="$1"
    value=${value//\\/\\\\}
    value=${value//\'/\\\'}
    printf '%s' "$value"
}
DB_PASS_SQL="$(escape_mysql_literal "$DB_PASS")"
mysql -uroot --protocol=socket --batch --skip-column-names <<SQL
CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS_SQL';
ALTER USER '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS_SQL';
GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost';
FLUSH PRIVILEGES;
SQL

# 4. Import cấu trúc SQL
echo "Import cấu trúc cơ sở dữ liệu..."
export MYSQL_PWD="$DB_PASS"
SCHEMA_EXISTS="$(mysql -N -s -h 127.0.0.1 -u "$DB_USER" "$DB_NAME" -e "SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'users'")"
if [[ "$SCHEMA_EXISTS" == "1" ]]; then
    echo "Schema đã tồn tại, bỏ qua import để bảo toàn dữ liệu."
else
    mysql --force=false -h 127.0.0.1 -u "$DB_USER" "$DB_NAME" < "$APP_PATH/vc_database/vc_vpn_web_commerce_mysql.sql"
fi
unset MYSQL_PWD

# 5. Cấu hình file .env
echo "Thiết lập cấu hình file .env..."
if [[ -f "$APP_PATH/.env" ]]; then
    cp "$APP_PATH/.env" "$APP_PATH/.env.backup.$(date +%Y%m%d%H%M%S)"
fi
cp "$APP_PATH/.env.example" "$APP_PATH/.env"
DB_NAME_ESCAPED="$(escape_sed_replacement "$DB_NAME")"
DB_USER_ESCAPED="$(escape_sed_replacement "$DB_USER")"
DB_PASS_ESCAPED="$(escape_sed_replacement "$DB_PASS")"
DOMAIN_ESCAPED="$(escape_sed_replacement "$DOMAIN")"
sed -i "s|^DB_DRIVER=.*|DB_DRIVER=mysql|; s|^DB_HOST=.*|DB_HOST=127.0.0.1|; s|^DB_PORT=.*|DB_PORT=3306|; s|^DB_DATABASE=.*|DB_DATABASE=$DB_NAME_ESCAPED|; s|^DB_USERNAME=.*|DB_USERNAME=$DB_USER_ESCAPED|; s|^DB_PASSWORD=.*|DB_PASSWORD=$DB_PASS_ESCAPED|; s|^APP_URL=.*|APP_URL=https://$DOMAIN_ESCAPED|" "$APP_PATH/.env"
unset DB_PASS

# 6. Cài đặt thư viện qua Composer
echo "Cài đặt thư viện bằng Composer..."
if ! command -v composer &> /dev/null; then
    php -r "copy('https://getcomposer.org/installer', '$APP_PATH/composer-setup.php');"
    php "$APP_PATH/composer-setup.php" --install-dir=/usr/local/bin --filename=composer
    rm -f "$APP_PATH/composer-setup.php"
fi
composer install --no-dev --optimize-autoloader --working-dir="$APP_PATH"

# 7. Khởi tạo và thiết lập phân quyền các thư mục cần thiết
echo "Thiết lập phân quyền bảo mật thư mục..."
mkdir -p "$APP_PATH/vc_storage/vc_cache" "$APP_PATH/vc_storage/vc_sessions" "$APP_PATH/vc_storage/vc_invoices" "$APP_PATH/vc_storage/vc_exports" "$APP_PATH/vc_storage/vc_temp"
mkdir -p "$APP_PATH/vc_logs/vc_app" "$APP_PATH/vc_logs/vc_payment" "$APP_PATH/vc_logs/vc_vpn" "$APP_PATH/vc_logs/vc_security" "$APP_PATH/vc_logs/vc_cron"

chown -R www:www "$APP_PATH"
find "$APP_PATH" -type d -exec chmod 755 {} \;
find "$APP_PATH" -type f -exec chmod 644 {} \;
chmod 640 "$APP_PATH/.env"
find "$APP_PATH" -maxdepth 1 -name '.env.backup.*' -exec chmod 600 {} \;
chmod -R 775 "$APP_PATH/vc_storage" "$APP_PATH/vc_public/vc_uploads" "$APP_PATH/vc_logs"

# 8. Tạo tài khoản quản trị sau khi import schema.
if [ -f "$APP_PATH/vc_scripts/create_admin.php" ]; then
    read -r -p "Email admin [admin@vc-vpn.local]: " ADMIN_EMAIL
    read -r -p "Username admin [admin]: " ADMIN_USERNAME
    read -r -s -p "Mật khẩu admin (bỏ trống dùng Admin@123456): " ADMIN_PASSWORD
    printf '\n'
    ADMIN_EMAIL="${ADMIN_EMAIL:-admin@vc-vpn.local}"
    ADMIN_USERNAME="${ADMIN_USERNAME:-admin}"
    ADMIN_PASSWORD="${ADMIN_PASSWORD:-Admin@123456}"
    echo "Tạo tài khoản quản trị..."
    sudo -u www env VC_ADMIN_EMAIL="$ADMIN_EMAIL" VC_ADMIN_USERNAME="$ADMIN_USERNAME" VC_ADMIN_PASSWORD="$ADMIN_PASSWORD" php "$APP_PATH/vc_scripts/create_admin.php"
fi

# 9. Thiết lập tự động hóa Cronjob đồng bộ với thư mục vc_cron
echo "Đăng ký các tiến trình Cronjob..."
CRON_LOG="$APP_PATH/vc_logs/vc_cron"
CRON_TRAFFIC="* * * * * cd $APP_PATH && sudo -u www php $APP_PATH/vc_cron/traffic_sync.php >> $CRON_LOG/traffic_sync.log 2>&1"
CRON_NODE="*/5 * * * * cd $APP_PATH && sudo -u www php $APP_PATH/vc_cron/node_health.php >> $CRON_LOG/node_health.log 2>&1"
CRON_SUB_RENEW="0 * * * * cd $APP_PATH && sudo -u www php $APP_PATH/vc_cron/subscription_renewal.php >> $CRON_LOG/subscription_renewal.log 2>&1"
CRON_SUB_EXPIRY="5 0 * * * cd $APP_PATH && sudo -u www php $APP_PATH/vc_cron/subscription_expiry.php >> $CRON_LOG/subscription_expiry.log 2>&1"
CRON_PAYMENT="*/5 * * * * cd $APP_PATH && sudo -u www php $APP_PATH/vc_cron/payment_check.php >> $CRON_LOG/payment_check.log 2>&1"
CRON_CLEANUP="0 2 * * * cd $APP_PATH && sudo -u www php $APP_PATH/vc_cron/cleanup.php >> $CRON_LOG/cleanup.log 2>&1"
CRON_BACKUP="0 3 * * * cd $APP_PATH && sudo -u www php $APP_PATH/vc_cron/backup.php >> $CRON_LOG/backup.log 2>&1"

(crontab -l 2>/dev/null | grep -v -F "$APP_PATH/vc_cron"; echo "$CRON_TRAFFIC"; echo "$CRON_NODE"; echo "$CRON_SUB_RENEW"; echo "$CRON_SUB_EXPIRY"; echo "$CRON_PAYMENT"; echo "$CRON_CLEANUP"; echo "$CRON_BACKUP") | crontab -

echo "================================================="
echo " Cài đặt mã nguồn hoàn tất thành công!"
echo "================================================="
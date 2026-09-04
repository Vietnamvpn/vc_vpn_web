#!/bin/bash
# vc_install.sh

set -Eeuo pipefail

APP_PATH="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)"

echo "================================================="
echo " Bắt đầu cài đặt tự động vc_vpn_web lên aaPanel"
echo " Đường dẫn hiện tại: $APP_PATH"
echo "================================================="

# 1. Thu thập thông tin
read -p "Nhập tên miền (Domain - ví dụ: vpn.domain.com): " DOMAIN
read -p "Nhập tên Database (PostgreSQL): " DB_NAME
read -p "Nhập User Database: " DB_USER
read -p "Nhập Mật khẩu Database: " DB_PASS

if [[ ! "$DB_NAME" =~ ^[a-zA-Z_][a-zA-Z0-9_]*$ || ! "$DB_USER" =~ ^[a-zA-Z_][a-zA-Z0-9_]*$ ]]; then
    echo "Lỗi: tên database và user chỉ được chứa chữ cái, số và dấu gạch dưới."
    exit 1
fi

# 2. Kiểm tra môi trường PostgreSQL
if ! command -v psql &> /dev/null; then
    echo "Lỗi: PostgreSQL chưa được cài đặt. Vui lòng cài đặt thông qua App Store của aaPanel."
    exit 1
fi

# 3. Tạo Database và User
echo "Khởi tạo Database và User..."
sudo -u postgres psql -c "CREATE USER $DB_USER WITH PASSWORD '$DB_PASS';"
sudo -u postgres psql -c "CREATE DATABASE $DB_NAME OWNER $DB_USER;"

# 4. Import cấu trúc SQL
echo "Import cấu trúc cơ sở dữ liệu..."
export PGPASSWORD="$DB_PASS"
psql -h 127.0.0.1 -U "$DB_USER" -d "$DB_NAME" -f "$APP_PATH/vc_database/vc_vpn_web_commerce.sql"
unset PGPASSWORD

# 5. Cấu hình file .env
echo "Thiết lập cấu hình file .env..."
cp .env.example .env
sed -i "s/DB_DRIVER=.*/DB_DRIVER=pgsql/" .env
sed -i "s/DB_HOST=.*/DB_HOST=127.0.0.1/" .env
sed -i "s/DB_PORT=.*/DB_PORT=5432/" .env
sed -i "s/DB_DATABASE=.*/DB_DATABASE=$DB_NAME/" .env
sed -i "s/DB_USERNAME=.*/DB_USERNAME=$DB_USER/" .env
sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=$DB_PASS/" .env
sed -i "s/APP_URL=.*/APP_URL=https:\/\/$DOMAIN/" .env

# 6. Cài đặt thư viện qua Composer
echo "Cài đặt thư viện bằng Composer..."
if ! command -v composer &> /dev/null; then
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer
    rm composer-setup.php
fi
composer install --no-dev --optimize-autoloader

# 7. Khởi tạo và thiết lập phân quyền các thư mục cần thiết
echo "Thiết lập phân quyền bảo mật thư mục..."
mkdir -p "$APP_PATH/vc_storage/vc_cache" "$APP_PATH/vc_storage/vc_sessions" "$APP_PATH/vc_storage/vc_invoices" "$APP_PATH/vc_storage/vc_exports" "$APP_PATH/vc_storage/vc_temp"
mkdir -p "$APP_PATH/vc_logs/vc_app" "$APP_PATH/vc_logs/vc_payment" "$APP_PATH/vc_logs/vc_vpn" "$APP_PATH/vc_logs/vc_security" "$APP_PATH/vc_logs/vc_cron"

chown -R www:www "$APP_PATH"
chmod -R 755 "$APP_PATH"
chmod -R 775 "$APP_PATH/vc_storage"
chmod -R 775 "$APP_PATH/vc_public/vc_uploads"
chmod -R 775 "$APP_PATH/vc_logs"

# 8. Tạo tài khoản quản trị sau khi import schema.
if [ -f "$APP_PATH/vc_scripts/create_admin.php" ]; then
    echo "Tạo tài khoản quản trị mặc định..."
    sudo -u www php "$APP_PATH/vc_scripts/create_admin.php"
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
#!/bin/bash
# vc_install.sh - Optimized & Clean CLI Interface

set -Eeuo pipefail

# Màu sắc hiển thị terminal
GREEN="\033[0;32m"
YELLOW="\033[1;33m"
RED="\033[0;31m"
CYAN="\033[0;36m"
NC="\033[0m" # No Color

APP_PATH="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)"

clear
echo -e "${CYAN}=================================================${NC}"
echo -e "${GREEN}      CÀI ĐẶT TỰ ĐỘNG VC_VPN_WEB LÊN AAPANEL     ${NC}"
echo -e "${CYAN}=================================================${NC}"
echo -e "Thư mục hiện tại: ${YELLOW}$APP_PATH${NC}\n"

# 1. Thu thập thông tin cấu hình
echo -e "${CYAN}[1/7] Nhập thông tin cấu hình hệ thống:${NC}"
read -r -p " ├── Tên miền (Domain - ví dụ: vpn.domain.com): " DOMAIN
read -r -p " ├── Tên Database (MySQL): " DB_NAME
read -r -p " ├── User Database: " DB_USER
read -r -s -p " └── Mật khẩu Database: " DB_PASS
printf '\n\n'

# Kiểm tra tính hợp lệ dữ liệu đầu vào
if [[ ! "$DB_NAME" =~ ^[a-zA-Z_][a-zA-Z0-9_]*$ || ! "$DB_USER" =~ ^[a-zA-Z_][a-zA-Z0-9_]*$ ]]; then
    echo -e "${RED}Lỗi: Tên database và user chỉ được chứa chữ cái, số và dấu gạch dưới.${NC}"
    exit 1
fi

if [[ -z "$DOMAIN" || ! "$DOMAIN" =~ ^[a-zA-Z0-9.-]+$ ]]; then
    echo -e "${RED}Lỗi: Domain không hợp lệ.${NC}"
    exit 1
fi

if [[ -z "$DB_PASS" ]]; then
    echo -e "${RED}Lỗi: Mật khẩu database không được để trống.${NC}"
    exit 1
fi

if [[ ! -f "$APP_PATH/.env.example" || ! -f "$APP_PATH/vc_database/vc_vpn_web_commerce_mysql.sql" ]]; then
    echo -e "${RED}Lỗi: Thiếu file .env.example hoặc schema MySQL trong gói mã nguồn.${NC}"
    exit 1
fi

escape_sed_replacement() {
    local value="$1"
    value=${value//\\/\\\\}
    value=${value//&/\\&}
    value=${value//|/\\|}
    printf '%s' "$value"
}

# 2. Kiểm tra môi trường hệ thống
if ! command -v mysql &> /dev/null || ! command -v php &> /dev/null; then
    echo -e "${RED}Lỗi: MySQL hoặc PHP chưa được cài đặt trên hệ thống.${NC}"
    exit 1
fi

# Tự động loại bỏ file cấu hình extension zip lỗi nếu tồn tại trên aaPanel
if [ -f "/www/server/php/84/etc/conf.d/zip.ini" ]; then
    rm -f /www/server/php/84/etc/conf.d/zip.ini
fi

if ! id www >/dev/null 2>&1; then
    echo -e "${RED}Lỗi: Không tìm thấy user 'www' của aaPanel.${NC}"
    exit 1
fi

if [[ "$(id -u)" -ne 0 ]]; then
    echo -e "${RED}Lỗi: Vui lòng chạy script bằng quyền root: sudo ./vc_install.sh${NC}"
    exit 1
fi

# 3 & 4. Import cấu trúc cơ sở dữ liệu
echo -e "${CYAN}[2/7] Khởi tạo cơ sở dữ liệu MySQL...${NC}"
export MYSQL_PWD="$DB_PASS"
SCHEMA_EXISTS="$(mysql -N -s -h 127.0.0.1 -u "$DB_USER" "$DB_NAME" -e "SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'users'")"
if [[ "$SCHEMA_EXISTS" == "1" ]]; then
    echo -e " ${YELLOW}ℹ Schema đã tồn tại, bỏ qua import để bảo toàn dữ liệu.${NC}"
else
    mysql --force=false -h 127.0.0.1 -u "$DB_USER" "$DB_NAME" < "$APP_PATH/vc_database/vc_vpn_web_commerce_mysql.sql"
    echo -e " ${GREEN}✔ Import cơ sở dữ liệu thành công.${NC}"
fi
unset MYSQL_PWD

# 5. Cấu hình file .env
echo -e "${CYAN}[3/7] Thiết lập tệp cấu hình môi trường (.env)...${NC}"
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
echo -e " ${GREEN}✔ Cấu hình .env hoàn tất.${NC}"

# 6. Cài đặt thư viện qua Composer
echo -e "${CYAN}[4/7] Cài đặt các gói phụ thuộc qua Composer...${NC}"
if ! command -v composer &> /dev/null; then
    php -r "copy('https://getcomposer.org/installer', '$APP_PATH/composer-setup.php');"
    php "$APP_PATH/composer-setup.php" --install-dir=/usr/local/bin --filename=composer
    rm -f "$APP_PATH/composer-setup.php"
fi
composer install --no-dev --optimize-autoloader --working-dir="$APP_PATH" > /dev/null 2>&1
echo -e " ${GREEN}✔ Hoàn tất cài đặt thư viện PHP.${NC}"

# 7. Thiết lập phân quyền thư mục
echo -e "${CYAN}[5/7] Thiết lập phân quyền bảo mật thư mục...${NC}"
mkdir -p "$APP_PATH/vc_storage/vc_cache" "$APP_PATH/vc_storage/vc_sessions" "$APP_PATH/vc_storage/vc_invoices" "$APP_PATH/vc_storage/vc_exports" "$APP_PATH/vc_storage/vc_temp"
mkdir -p "$APP_PATH/vc_logs/vc_app" "$APP_PATH/vc_logs/vc_payment" "$APP_PATH/vc_logs/vc_vpn" "$APP_PATH/vc_logs/vc_security" "$APP_PATH/vc_logs/vc_cron"
mkdir -p "$APP_PATH/vc_public/vc_uploads/avatars" "$APP_PATH/vc_public/vc_uploads/invoices" "$APP_PATH/vc_public/vc_uploads/attachments"

chown -R www:www "$APP_PATH"
find "$APP_PATH" -type d -exec chmod 755 {} \;
find "$APP_PATH" -type f -exec chmod 644 {} \;
chmod 640 "$APP_PATH/.env"
find "$APP_PATH" -maxdepth 1 -name '.env.backup.*' -exec chmod 600 {} \;
chmod -R 775 "$APP_PATH/vc_storage" "$APP_PATH/vc_public/vc_uploads" "$APP_PATH/vc_logs"
echo -e " ${GREEN}✔ Phân quyền bảo mật thành công.${NC}"

# 8. Tạo tài khoản quản trị
echo -e "${CYAN}[6/7] Thiết lập tài khoản quản trị hệ thống:${NC}"
if [ -f "$APP_PATH/vc_scripts/create_admin.php" ]; then
    read -r -p " ├── Email quản trị [admin@vc-vpn.local]: " ADMIN_EMAIL
    read -r -p " ├── Tên đăng nhập [admin]: " ADMIN_USERNAME
    read -r -s -p " └── Mật khẩu quản trị (để trống dùng mặc định): " ADMIN_PASSWORD
    printf '\n\n'
    
    ADMIN_EMAIL="${ADMIN_EMAIL:-admin@vc-vpn.local}"
    ADMIN_USERNAME="${ADMIN_USERNAME:-admin}"
    ADMIN_PASSWORD="${ADMIN_PASSWORD:-Admin@123456}"
    
    sudo -u www env VC_ADMIN_EMAIL="$ADMIN_EMAIL" VC_ADMIN_USERNAME="$ADMIN_USERNAME" VC_ADMIN_PASSWORD="$ADMIN_PASSWORD" php "$APP_PATH/vc_scripts/create_admin.php"
    echo -e " ${GREEN}✔ Thiết lập tài khoản quản trị hoàn tất.${NC}"
fi

# 9. Đăng ký Cronjob tự động hóa
echo -e "${CYAN}[7/7] Đăng ký các tiến trình Cronjob định kỳ...${NC}"
CRON_LOG="$APP_PATH/vc_logs/vc_cron"
CRON_TRAFFIC="* * * * * cd $APP_PATH && sudo -u www php $APP_PATH/vc_cron/traffic_sync.php >> $CRON_LOG/traffic_sync.log 2>&1"
CRON_NODE="*/5 * * * * cd $APP_PATH && sudo -u www php $APP_PATH/vc_cron/node_health.php >> $CRON_LOG/node_health.log 2>&1"
CRON_SUB_RENEW="0 * * * * cd $APP_PATH && sudo -u www php $APP_PATH/vc_cron/subscription_renewal.php >> $CRON_LOG/subscription_renewal.log 2>&1"
CRON_SUB_EXPIRY="5 0 * * * cd $APP_PATH && sudo -u www php $APP_PATH/vc_cron/subscription_expiry.php >> $CRON_LOG/subscription_expiry.log 2>&1"
CRON_PAYMENT="*/5 * * * * cd $APP_PATH && sudo -u www php $APP_PATH/vc_cron/payment_check.php >> $CRON_LOG/payment_check.log 2>&1"
CRON_CLEANUP="0 2 * * * cd $APP_PATH && sudo -u www php $APP_PATH/vc_cron/cleanup.php >> $CRON_LOG/cleanup.log 2>&1"
CRON_BACKUP="0 3 * * * cd $APP_PATH && sudo -u www php $APP_PATH/vc_cron/backup.php >> $CRON_LOG/backup.log 2>&1"

(crontab -l 2>/dev/null | grep -v -F "$APP_PATH/vc_cron"; echo "$CRON_TRAFFIC"; echo "$CRON_NODE"; echo "$CRON_SUB_RENEW"; echo "$CRON_SUB_EXPIRY"; echo "$CRON_PAYMENT"; echo "$CRON_CLEANUP"; echo "$CRON_BACKUP") | crontab -
echo -e " ${GREEN}✔ Đăng ký Cronjob hoàn tất.${NC}"

echo -e "\n${CYAN}================================================="
echo -e "${GREEN}      CÀI ĐẶT VÀ CẤU HÌNH THÀNH CÔNG 100%!       ${NC}"
echo -e "${CYAN}=================================================${NC}"
#!/bin/bash
# vc_update.sh - Optimized & Clean CLI Interface

set -Eeuo pipefail

# Màu sắc hiển thị terminal
GREEN="\033[0;32m"
YELLOW="\033[1;33m"
RED="\033[0;31m"
CYAN="\033[0;36m"
NC="\033[0m" # No Color

APP_PATH="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)"

# Kiểm tra quyền root
if [[ "$(id -u)" -ne 0 ]]; then
    echo -e "${RED}Lỗi: Vui lòng chạy script bằng quyền root: sudo ./vc_update.sh${NC}"
    exit 1
fi

clear
echo -e "${CYAN}=================================================${NC}"
echo -e "${GREEN}      CẬP NHẬT MÃ NGUỒN VC_VPN_WEB TỰ ĐỘNG       ${NC}"
echo -e "${CYAN}=================================================${NC}"
echo -e "Thư mục hiện tại: ${YELLOW}$APP_PATH${NC}\n"

# Tự động loại bỏ file cấu hình extension zip lỗi nếu tồn tại trên aaPanel
if [ -f "/www/server/php/84/etc/conf.d/zip.ini" ]; then
    rm -f /www/server/php/84/etc/conf.d/zip.ini
fi

# 1. Kéo mã nguồn mới từ Git
echo -e "${CYAN}[1/5] Tải mã nguồn mới từ kho Git...${NC}"
if [ -d "$APP_PATH/.git" ]; then
    git checkout -- . > /dev/null 2>&1 || true
    git pull origin main
    echo -e " ${GREEN}✔ Cập nhật mã nguồn Git thành công.${NC}"
else
    echo -e " ${YELLOW}ℹ Thư mục không phải repository Git, bỏ qua bước pull.${NC}"
fi

# 2. Cập nhật thư viện Composer
echo -e "${CYAN}[2/5] Cập nhật các gói thư viện Composer...${NC}"
if command -v composer &> /dev/null; then
    composer install --no-dev --optimize-autoloader --working-dir="$APP_PATH" > /dev/null 2>&1
    echo -e " ${GREEN}✔ Cập nhật thư viện thành công.${NC}"
else
    echo -e " ${YELLOW}ℹ Composer chưa được cài đặt, bỏ qua bước này.${NC}"
fi

# 3. Cập nhật cơ sở dữ liệu (Migration)
echo -e "${CYAN}[3/5] Cập nhật cấu trúc cơ sở dữ liệu...${NC}"
if [ -f "$APP_PATH/vc_scripts/migrate.php" ]; then
    # Nếu hệ thống dùng MySQL, tự động loại bỏ các file migration chứa cú pháp PostgreSQL (như pgcrypto)
    if [ -f "$APP_PATH/.env" ] && grep -q "DB_DRIVER=mysql" "$APP_PATH/.env"; then
        find "$APP_PATH" -type f -name "*.sql" -exec grep -l "pgcrypto" {} \; 2>/dev/null | while read -r pg_file; do
            rm -f "$pg_file"
        done
    fi

    if id www >/dev/null 2>&1; then
        sudo -u www php "$APP_PATH/vc_scripts/migrate.php"
    else
        php "$APP_PATH/vc_scripts/migrate.php"
    fi
    echo -e " ${GREEN}✔ Migration cơ sở dữ liệu hoàn tất.${NC}"
else
    echo -e " ${YELLOW}ℹ Không tìm thấy file migrate.php, bỏ qua.${NC}"
fi

# 4. Xóa Cache hệ thống
echo -e "${CYAN}[4/5] Dọn dẹp bộ nhớ đệm ứng dụng...${NC}"
if [ -f "$APP_PATH/vc_scripts/clear_cache.php" ]; then
    if id www >/dev/null 2>&1; then
        sudo -u www php "$APP_PATH/vc_scripts/clear_cache.php"
    else
        php "$APP_PATH/vc_scripts/clear_cache.php"
    fi
    echo -e " ${GREEN}✔ Dọn dẹp cache hoàn tất.${NC}"
else
    echo -e " ${YELLOW}ℹ Không tìm thấy file clear_cache.php, bỏ qua.${NC}"
fi

# 5. Thiết lập phân quyền thư mục
echo -e "${CYAN}[5/5] Thiết lập lại phân quyền thư mục...${NC}"
if id www >/dev/null 2>&1; then
    chown -R www:www "$APP_PATH"
fi
find "$APP_PATH" -type d -exec chmod 755 {} \;
find "$APP_PATH" -type f -exec chmod 644 {} \;
if [ -f "$APP_PATH/.env" ]; then
    chmod 640 "$APP_PATH/.env"
fi
mkdir -p "$APP_PATH/vc_storage" "$APP_PATH/vc_public/vc_uploads" "$APP_PATH/vc_logs"
chmod -R 775 "$APP_PATH/vc_storage" "$APP_PATH/vc_public/vc_uploads" "$APP_PATH/vc_logs"
echo -e " ${GREEN}✔ Phân quyền hệ thống hoàn tất.${NC}"

echo -e "\n${CYAN}================================================="
echo -e "${GREEN}      CẬP NHẬT MÃ NGUỒN THÀNH CÔNG 100%!        ${NC}"
echo -e "${CYAN}=================================================${NC}"
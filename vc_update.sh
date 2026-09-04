#!/bin/bash
# vc_update.sh

set -Eeuo pipefail

APP_PATH="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)"

echo "================================================="
echo " Bắt đầu cập nhật mã nguồn vc_vpn_web"
echo " Đường dẫn hiện tại: $APP_PATH"
echo "================================================="

# 1. Kéo mã nguồn mới nhất (Bỏ qua các sửa đổi cục bộ nếu có)
echo "Tải mã nguồn mới từ kho Git..."
git checkout -- .
git pull origin main

# 2. Cập nhật thư viện
echo "Cập nhật các gói thư viện Composer..."
composer install --no-dev --optimize-autoloader

# 3. Chạy cập nhật dữ liệu (Migration)
echo "Cập nhật cấu trúc cơ sở dữ liệu (nếu có)..."
if [ -f "$APP_PATH/vc_scripts/migrate.php" ]; then
    sudo -u www php "$APP_PATH/vc_scripts/migrate.php"
fi

# 4. Xóa Cache hệ thống
echo "Dọn dẹp bộ nhớ đệm ứng dụng..."
if [ -f "$APP_PATH/vc_scripts/clear_cache.php" ]; then
    sudo -u www php "$APP_PATH/vc_scripts/clear_cache.php"
fi

# 5. Khôi phục quyền sở hữu để tránh lỗi hệ thống ghi đè
echo "Thiết lập lại phân quyền thư mục..."
chown -R www:www "$APP_PATH"
chmod -R 755 "$APP_PATH"
chmod -R 775 "$APP_PATH/vc_storage"
chmod -R 775 "$APP_PATH/vc_public/vc_uploads"
chmod -R 775 "$APP_PATH/vc_logs"

echo "================================================="
echo " Cập nhật mã nguồn hoàn tất!"
echo "================================================="
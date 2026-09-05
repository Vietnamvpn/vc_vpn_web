<?php
/**
 * Điểm vào công khai duy nhất của toàn bộ hệ thống (Single Entry Point)
 */

// Khởi động phiên làm việc (Session) bảo mật
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cơ chế tự động nạp lớp (Autoloader) theo chuẩn Namespace
spl_autoload_register(function ($class) {
    // Xử lý namespace cho VcCore (vc_app/vc_core/)
    $corePrefix = 'VcCore\\';
    $coreDir = __DIR__ . '/../vc_app/vc_core/';
    
    if (strncmp($corePrefix, $class, strlen($corePrefix)) === 0) {
        $relativeClass = substr($class, strlen($corePrefix));
        $file = $coreDir . str_replace('\\', '/', $relativeClass) . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }

    // Xử lý namespace cho phần ứng dụng chung VcApp\ (Controllers, Middlewares, Models...)
    $appPrefix = 'VcApp\\';
    $appDir = __DIR__ . '/../vc_app/';
    
    if (strncmp($appPrefix, $class, strlen($appPrefix)) === 0) {
        $relativeClass = substr($class, strlen($appPrefix));
        $parts = explode('\\', $relativeClass);
        
        // Tự động chuyển đổi tên thư mục từ PascalCase (ví dụ: VcControllers) sang chuẩn Linux (vc_controllers)
        if (isset($parts[0])) {
            $parts[0] = strtolower(preg_replace('/^Vc([A-Z])/', 'vc_$1', $parts[0]));
        }
        
        $file = $appDir . implode('/', $parts) . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Khởi tạo và chạy ứng dụng
use VcCore\Application;

$app = new Application();
$app->run();
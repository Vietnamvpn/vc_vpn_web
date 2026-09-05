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
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Admin - VC VPN Web'; ?></title>
    <link rel="stylesheet" href="/vc_assets/vc_css/admin.css">
</head>
<body class="admin-theme">
    <div class="admin-wrapper">
        <?php 
        $sidebarPath = APP_BASE_PATH . '/vc_views/vc_components/sidebar.php';
        if (file_exists($sidebarPath)) require_once $sidebarPath; 
        ?>
        
        <div class="admin-main">
            <?php 
            $navbarPath = APP_BASE_PATH . '/vc_views/vc_components/navbar.php';
            if (file_exists($navbarPath)) require_once $navbarPath; 
            ?>
            
            <div class="admin-content">
                <?php echo isset($content) ? $content : ''; ?>
            </div>
        </div>
    </div>
    <script src="/vc_assets/vc_js/admin.js"></script>
</body>
</html>
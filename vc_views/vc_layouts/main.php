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
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'VC VPN Web'; ?></title>
    <link rel="stylesheet" href="/vc_assets/vc_css/app.css">
</head>
<body>
    <?php 
    $headerPath = APP_BASE_PATH . '/vc_views/vc_components/header.php';
    if (file_exists($headerPath)) {
        require_once $headerPath;
    }
    ?>
    
    <main>
        <?php echo isset($content) ? $content : ''; ?>
    </main>

    <?php 
    $footerPath = APP_BASE_PATH . '/vc_views/vc_components/footer.php';
    if (file_exists($footerPath)) {
        require_once $footerPath;
    }
    ?>
    <script src="/vc_assets/vc_js/app.js"></script>
</body>
</html>
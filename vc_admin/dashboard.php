<?php
/**
 * vc_admin/dashboard.php
 * Admin Panel Dashboard Entry Point
 */

require_once __DIR__ . '/bootstrap.php';
vc_admin_require_authentication();

$username = htmlspecialchars($_SESSION['admin_username'] ?? 'Administrator');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - VC VPN</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f4f6f9; margin: 0; padding: 40px; }
        .dashboard-container { max-width: 800px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        h1 { color: #333; margin-top: 0; }
        p { color: #666; line-height: 1.5; }
        .logout-btn { display: inline-block; padding: 10px 20px; background: #dc3545; color: #fff; text-decoration: none; border-radius: 4px; margin-top: 20px; font-weight: 500; }
        .logout-btn:hover { background: #c82333; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h1>Welcome to Admin Dashboard, <?php echo $username; ?>!</h1>
        <p>You have successfully logged into the <strong>vc_vpn_web</strong> management system. The core structure is active and operating on PostgreSQL.</p>
        <a href="<?php echo htmlspecialchars(vc_admin_url('logout'), ENT_QUOTES, 'UTF-8'); ?>" class="logout-btn">Logout</a>
    </div>
</body>
</html>
<script>
    if (window.history.replaceState) {
        window.history.replaceState({}, document.title, '/#/dashboard');
    }
</script>
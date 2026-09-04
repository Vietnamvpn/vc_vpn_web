<?php
require_once __DIR__ . '/bootstrap.php';

if (vc_admin_is_authenticated()) {
    header('Location: ' . vc_admin_url('dashboard'));
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        try {
            $stmt = vc_admin_database()->prepare("SELECT u.* FROM users u JOIN user_roles ur ON u.id = ur.user_id JOIN roles r ON ur.role_id = r.id WHERE (u.username = ? OR u.email = ?) AND u.status = 'active' AND r.name IN ('super_admin', 'admin') LIMIT 1");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_user_id'] = $user['id'];
                $_SESSION['admin_username'] = $user['username'];
                session_regenerate_id(true);
                header('Location: ' . vc_admin_url('dashboard'));
                exit;
            } else {
                $error = 'Invalid credentials or insufficient privileges.';
            }
        } catch (PDOException | RuntimeException $e) {
            $error = 'Unable to connect to the database. Check the .env configuration.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - VC VPN</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f4f6f9; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-card { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        .login-card h2 { margin-bottom: 20px; color: #333; text-align: center; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; color: #666; font-size: 14px; }
        .form-group input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; font-size: 14px; }
        .btn { width: 100%; padding: 10px; background: #007bff; border: none; color: #fff; border-radius: 4px; font-size: 16px; cursor: pointer; }
        .btn:hover { background: #0056b3; }
        .error { color: #dc3545; margin-bottom: 15px; text-align: center; font-size: 14px; background: #f8d7da; padding: 8px; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="login-card">
        <h2>Admin Login</h2>
        <?php if (!empty($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form action="<?php echo htmlspecialchars(vc_admin_url('login'), ENT_QUOTES, 'UTF-8'); ?>" method="POST">
            <div class="form-group">
                <label for="username">Username or Email</label>
                <input type="text" id="username" name="username" required autocomplete="username">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required autocomplete="current-password">
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
    </div>
</body>
</html>
<script>
    if (window.history.replaceState) {
        window.history.replaceState({}, document.title, '/#/login');
    }
</script>
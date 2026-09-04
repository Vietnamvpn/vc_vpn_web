<?php
require_once __DIR__ . '/bootstrap.php';

header('Location: ' . (vc_admin_is_authenticated() ? 'dashboard.php' : 'login.php'));
exit;

<?php
if (!defined('APP_BASE_PATH')) {
    header("HTTP/1.0 403 Forbidden");
    exit('Direct access forbidden.');
}
$successMessage = $_SESSION['success'] ?? null;
$errorMessage = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);
?>
<?php if ($successMessage): ?>
    <div class="alert alert-success">
        <span><?php echo htmlspecialchars($successMessage); ?></span>
        <button type="button" class="alert-close" onclick="this.parentElement.style.display='none';">&times;</button>
    </div>
<?php endif; ?>

<?php if ($errorMessage): ?>
    <div class="alert alert-danger">
        <span><?php echo htmlspecialchars($errorMessage); ?></span>
        <button type="button" class="alert-close" onclick="this.parentElement.style.display='none';">&times;</button>
    </div>
<?php endif; ?>
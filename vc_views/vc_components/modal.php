<?php
if (!defined('APP_BASE_PATH')) {
    header("HTTP/1.0 403 Forbidden");
    exit('Direct access forbidden.');
}
$modalId = $modalId ?? 'commonModal';
$modalTitle = $modalTitle ?? 'Thông Báo';
?>
<div id="<?php echo htmlspecialchars($modalId); ?>" class="modal-overlay" style="display: none;">
    <div class="modal-container">
        <div class="modal-header">
            <h3><?php echo htmlspecialchars($modalTitle); ?></h3>
            <button type="button" class="modal-close" onclick="document.getElementById('<?php echo htmlspecialchars($modalId); ?>').style.display='none';">&times;</button>
        </div>
        <div class="modal-body">
            <?php echo isset($modalContent) ? $modalContent : ''; ?>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="document.getElementById('<?php echo htmlspecialchars($modalId); ?>').style.display='none';">Đóng</button>
        </div>
    </div>
</div>
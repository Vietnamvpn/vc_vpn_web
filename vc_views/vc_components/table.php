<?php
if (!defined('APP_BASE_PATH')) {
    header("HTTP/1.0 403 Forbidden");
    exit('Direct access forbidden.');
}
$columns = $columns ?? [];
$rows = $rows ?? [];
?>
<div class="table-responsive">
    <table class="data-table">
        <thead>
            <tr>
                <?php foreach ($columns as $col): ?>
                    <th><?php echo htmlspecialchars($col); ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($rows)): ?>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <?php foreach ($row as $cell): ?>
                            <td><?php echo $cell; ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="<?php echo count($columns) > 0 ? count($columns) : 1; ?>" class="text-center">Không có dữ liệu hiển thị.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
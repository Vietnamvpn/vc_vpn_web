<?php
if (!defined('APP_BASE_PATH')) {
    header("HTTP/1.0 403 Forbidden");
    exit('Direct access forbidden.');
}
$currentPage = $currentPage ?? 1;
$totalPages = $totalPages ?? 1;
$baseUrl = $baseUrl ?? '#';
if ($totalPages > 1):
?>
<nav class="pagination-container">
    <ul class="pagination">
        <?php if ($currentPage > 1): ?>
            <li><a href="<?php echo $baseUrl; ?>?page=<?php echo $currentPage - 1; ?>">&laquo; Trước</a></li>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="<?php echo ($i === $currentPage) ? 'active' : ''; ?>">
                <a href="<?php echo $baseUrl; ?>?page=<?php echo $i; ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>

        <?php if ($currentPage < $totalPages): ?>
            <li><a href="<?php echo $baseUrl; ?>?page=<?php echo $currentPage + 1; ?>">Sau &raquo;</a></li>
        <?php endif; ?>
    </ul>
</nav>
<?php endif; ?>
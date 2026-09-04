<?php
/**
 * vc_scripts/clear_cache.php
 * Clears application cache and temporary files.
 */

if (php_sapi_name() !== 'cli') {
    die("This script can only be run from the command line.");
}

$cacheDir = __DIR__ . '/../vc_storage/vc_cache';
$tempDir = __DIR__ . '/../vc_storage/vc_temp';

$clearedCount = 0;

foreach ([$cacheDir, $tempDir] as $dir) {
    if (is_dir($dir)) {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $fileinfo) {
            if ($fileinfo->isDir()) {
                rmdir($fileinfo->getRealPath());
            } else {
                unlink($fileinfo->getRealPath());
                $clearedCount++;
            }
        }
    }
}

echo "Cache cleared successfully. Removed $clearedCount temporary cache files.\n";
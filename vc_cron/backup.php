<?php
/**
 * vc_cron/backup.php
 * Wrapper script for automated cron scheduled backups.
 */

if (php_sapi_name() !== 'cli') {
    die("This script can only be run from the command line.");
}

require_once __DIR__ . '/../vc_scripts/backup.php';
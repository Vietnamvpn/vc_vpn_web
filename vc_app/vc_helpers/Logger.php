<?php

namespace VcApp\VcHelpers;

class Logger
{
    public static function log($message, $level = 'INFO')
    {
        $date = date('Y-m-d');
        $logDir = __DIR__ . '/../../vc_logs/vc_app/';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        $logFile = $logDir . "app_{$date}.log";
        $timestamp = date('Y-m-d H:i:s');
        $formattedMessage = "[{$timestamp}] [{$level}] {$message}" . PHP_EOL;
        file_put_contents($logFile, $formattedMessage, FILE_APPEND);
    }
}
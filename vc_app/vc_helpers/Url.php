<?php

namespace VcApp\VcHelpers;

class Url
{
    public static function to($path = '')
    {
        $baseUrl = rtrim($_ENV['APP_URL'] ?? '', '/');
        $path = ltrim($path, '/');
        return $baseUrl ? "{$baseUrl}/{$path}" : "/{$path}";
    }

    public static function redirect($path)
    {
        header("Location: " . self::to($path));
        exit;
    }
}
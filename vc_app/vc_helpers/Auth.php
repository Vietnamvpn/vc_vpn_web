<?php

namespace VcApp\VcHelpers;

class Auth
{
    public static function check()
    {
        return isset($_SESSION['user_id']);
    }

    public static function id()
    {
        return $_SESSION['user_id'] ?? null;
    }

    public static function user()
    {
        return $_SESSION['user'] ?? null;
    }

    public static function adminCheck()
    {
        return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
    }
}
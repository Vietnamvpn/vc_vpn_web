<?php

namespace VcApp\VcHelpers;

class Token
{
    public static function generate($length = 32)
    {
        return bin2hex(random_bytes($length));
    }

    public static function numeric($length = 6)
    {
        $min = pow(10, $length - 1);
        $max = pow(10, $length) - 1;
        return (string) random_int($min, $max);
    }
}
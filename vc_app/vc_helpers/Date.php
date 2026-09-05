<?php

namespace VcApp\VcHelpers;

class Date
{
    public static function now()
    {
        return date('Y-m-d H:i:s');
    }

    public static function format($datetime, $format = 'd/m/Y H:i:s')
    {
        if (empty($datetime)) return '';
        return date($format, strtotime($datetime));
    }
}
<?php

namespace VcApp\VcHelpers;

class Money
{
    public static function format($amount, $currency = 'VND')
    {
        if ($currency === 'VND') {
            return number_format($amount, 0, ',', '.') . ' đ';
        }
        return '$' . number_format($amount, 2, '.', ',');
    }
}
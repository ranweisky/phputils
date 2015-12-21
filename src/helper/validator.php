<?php

namespace Xiaoju\Beatles\Utils;

class Validator
{

    public static function isDigit($text)
    {
        return ctype_digit($text);
    }

    public static function isInt($text)
    {
        return preg_match('/^-?\d+$/', $text) == 1;
    }

    public static function isXDigit($text)
    {
        return ctype_xdigit($text);
    }

    public static function isFloat($text)
    {
        return preg_match('/^(-?\d+)(\.\d+)?$/', $text) == 1;
    }

    public static function isIp($text)
    {
        return preg_match(
            //@codingStandardsIgnoreLine long constant
            '/^(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])$/',
            $text
        ) == 1;
    }

    public static function isAlpha($text)
    {
        return ctype_alpha($text);
    }

    public static function isDigitWithAlpha($text)
    {
        return ctype_alnum($text);
    }

    public static function isCellphone($text)
    {
        return preg_match('/^1\d{10}$/', $text) == 1;
    }

    public static function isNonSpace($text)
    {
        return preg_match('/^[^\s]+$/', $text) == 1;
    }

    public static function isVersion($text)
    {
        return preg_match('/^(\d)(\.\d){1,3}[a-zA-Z]?$/', $text) == 1;
    }
}

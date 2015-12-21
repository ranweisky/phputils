<?php

namespace Xiaoju\Beatles\Utils;

class Authentication
{
    const AUTHENTICATION_KEY = 'NEVER_EXIST_THIS_AUTHENTICATION_KEY';

    public static function createSign($key, $args)
    {
        if (!is_string($key) || !is_array($args)) {
            throw new \InvalidArgumentException();
        }

        $sign = self::generateSign($key, $args);
        return $sign;
    }

    public static function verify($key, $args, $sign)
    {
        if (!is_string($key) || !is_array($args) || !is_string($sign)) {
            throw new \InvalidArgumentException();
        }

        $actualSign = self::generateSign($key, $args);
        return (strcmp($actualSign, $sign) == 0);
    }

    /**
     * @param $key
     * @param $args
     * @return string
     */
    private static function generateSign($key, $args)
    {
        $args[self::AUTHENTICATION_KEY] = $key;
        ksort($args);
        $value = serialize($args);
        $sign = md5($value);
        return $sign;
    }
}

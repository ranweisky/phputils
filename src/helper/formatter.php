<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2014/12/31
 * Time: 11:23
 */
namespace Xiaoju\Beatles\Utils;

class Formatter
{
    const FORMAT_JSON = 'json';

    public static function format($data, $format = self::FORMAT_JSON)
    {
        if (!is_array($data)) {
            throw new \InvalidArgumentException();
        }
        switch ($format) {
            default:
                return json_encode(self::formatArray($data));
        }
    }

    private static function formatArray($data)
    {
     //FIXME
        #if (is_null($data)) {
        #    throw new \InvalidArgumentException('val is null');
        #}
        if (!is_array($data)) {
            if(is_string($data))
            {
                $data = mb_convert_encoding($data, 'UTF-8', 'UTF-8');
            }
            return strval($data);
        }
        foreach ($data as $k => $v) {
            $data[$k] = self::formatArray($v);
        }
        return $data;
    }
}

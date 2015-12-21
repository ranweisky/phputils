<?php

namespace Xiaoju\Beatles\Utils;

/**
 * Class HttpRequest
 * @package Xiaoju\Beatles\Utils
 */
class HttpRequest
{
    const CURL_SUCCESS_CODE = 0;

    /**
     * @param string $url
     * @param string $tag - tag name
     * @param int $timeout - total timeout in ms
     * @param int $connectionTimeout - connection timeout in ms
     * @param array $header
     * @return string result
     * @throws \InvalidArgumentException - invalid argument
     * @throws \Exception - get execution failed
     */
    public static function get(
        $url,
        $tag = '',
        $timeout = 0,
        $connectionTimeout = 0,
        $header = array(
            'Connection: Keep-Alive',
            'Content-type: application/x-www-form-urlencoded;charset=UTF-8'
        )
    ) {
        if (!is_string($url) ||
            !is_string($tag) ||
            !is_int($timeout) ||
            !is_int($connectionTimeout) ||
            !is_array($header)
        ) {
            throw new \InvalidArgumentException();
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($connectionTimeout > 0) {
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, $connectionTimeout);
        }
        if ($timeout > 0) {
            curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT_MS, $timeout);
        }
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $ret = curl_exec($ch);
        $benchmark = curl_getinfo($ch);
        $logErrorNo = curl_errno($ch);
        if ($logErrorNo) {
            curl_close($ch);
            self::log('http request get failed', $logErrorNo, $url, $tag, $benchmark);
            throw new \Exception('Get fail');
        } else {
            self::log('http request get succeeded', self::CURL_SUCCESS_CODE, $url, $tag, $benchmark);
        }
        curl_close($ch);
        return $ret;
    }

    /**
     * @param $url
     * @param $data - post arguments
     * @param string $tag - tag name
     * @param int $timeout - total timeout in ms
     * @param int $connectionTimeout - connection timeout in ms
     * @param array $header
     * @return string result
     * @throws \InvalidArgumentException - invalid argument
     * @throws \Exception - post execution failed
     */
    public static function post(
        $url,
        $data,
        $tag = '',
        $timeout = 0,
        $connectionTimeout = 0,
        $header = array(
            'Connection: Keep-Alive',
            'Content-type: application/x-www-form-urlencoded;charset=UTF-8'
        )
    ) {
        if (!is_string($url) ||
            !is_string($tag) ||
            !is_int($timeout) ||
            !is_int($connectionTimeout) ||
            !is_array($data) ||
            !is_array($header)
        ) {
            throw new \InvalidArgumentException();
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        if ($connectionTimeout > 0) {
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, $connectionTimeout);
        }
        if ($timeout > 0) {
            curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT_MS, $timeout);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        $args = http_build_query($data);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
        $ret = curl_exec($ch);
        $benchmark = curl_getinfo($ch);
        $logErrorNo = curl_errno($ch);
        if ($logErrorNo) {
            curl_close($ch);
            self::log('http request post failed', $logErrorNo, $url, $tag, $benchmark);
            throw new \Exception('Post fail');
        } else {
            self::log('http request post succeeded', self::CURL_SUCCESS_CODE, $url, $tag, $benchmark);
        }
        curl_close($ch);

        return $ret;
    }

    /**
     * @param $url
     * @param $data - post arguments
     * @param string $tag - tag name
     * @param int $timeout - total timeout in ms
     * @param int $connectionTimeout - connection timeout in ms
     * @param array $header
     * @return string result
     * @throws \InvalidArgumentException - invalid argument
     * @throws \Exception - post execution failed
     */
    public static function postFile(
        $url,
        $data,
        $tag = '',
        $timeout = 0,
        $connectionTimeout = 0,
        $header = array(
            'Connection: Keep-Alive',
            'Content-type: application/x-www-form-urlencoded;charset=UTF-8'
        )
    ) {
        if (!is_string($url) ||
            !is_string($tag) ||
            !is_int($timeout) ||
            !is_int($connectionTimeout) ||
            !is_array($data) ||
            !is_array($header)
        ) {
            throw new \InvalidArgumentException();
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        if ($connectionTimeout > 0) {
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, $connectionTimeout);
        }
        if ($timeout > 0) {
            curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT_MS, $timeout);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $ret = curl_exec($ch);
        $benchmark = curl_getinfo($ch);
        $logErrorNo = curl_errno($ch);
        if ($logErrorNo) {
            curl_close($ch);
            self::log('http request post failed', $logErrorNo, $url, $tag, $benchmark);
            throw new \Exception('Post fail');
        } else {
            self::log('http request post succeeded', self::CURL_SUCCESS_CODE, $url, $tag, $benchmark);
        }
        curl_close($ch);

        return $ret;
    }

    /**
     * @param $url
     * @param $data - post arguments
     * @param string $tag - tag name
     * @param int $timeout - total timeout in ms
     * @param int $connectionTimeout - connection timeout in ms
     * @param array $header
     * @return string result
     * @throws \InvalidArgumentException - invalid argument
     * @throws \Exception - post execution failed
     */
    public static function postJson(
        $url,
        $args,
        $tag = '',
        $timeout = 0,
        $connectionTimeout = 0
    ) {
        if (!is_string($url) ||
            !is_string($tag) ||
            !is_int($timeout) ||
            !is_int($connectionTimeout) ||
            !is_array($args)
        ) {
            throw new \InvalidArgumentException();
        }

        $ch = curl_init($url);
        if ($connectionTimeout > 0) {
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, $connectionTimeout);
        }
        if ($timeout > 0) {
            curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT_MS, $timeout);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        $data = json_encode($args);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=utf-8',
            'Content-Length: '.strlen($data))
        );
        $ret = curl_exec($ch);
        $benchmark = curl_getinfo($ch);                                                                                                                                                                                        ($ch);
        $logErrorNo = curl_errno($ch);
        if ($logErrorNo) {
            curl_close($ch);
            self::log('http request post failed', $logErrorNo, $url, $tag, $benchmark);
            throw new \Exception('Post json fail');
        } else {
            self::log('http request post json succeeded', self::CURL_SUCCESS_CODE, $url, $tag, $benchmark);
        }
        curl_close($ch);

        return $ret;
    }

    /**
     * @param $msg
     * @param $errorNo
     * @param $url
     * @param $tag
     * @param $benchmark
     */
    private static function log($msg, $errorNo, $url, $tag, $benchmark)
    {
        if (class_exists('Xiaoju\Beatles\Utils\Logger')) {
            $logArgs = array('tag' => $tag, 'url' => $url);
            unset($benchmark['certinfo']);
            $timeTags = array(
                'total_time',
                'namelookup_time',
                'connect_time',
                'pretransfer_time',
                'starttransfer_time',
                'redirect_time',
            );
            foreach ($timeTags as $tag) {
                $benchmark[$tag] .= 's';
            }
            $logArgs = array_merge($logArgs, $benchmark);
            Logger::warning($msg, $errorNo, $logArgs);
        }
    }
}

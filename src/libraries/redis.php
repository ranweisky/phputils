<?php

namespace Xiaoju\Beatles\Utils;

class Redis
{
    const DEFAULT_IP = '127.0.0.1';
    const DEFAULT_PORT = 6379;
    const DEFAULT_INDEX = 0;
    const DEFAULT_KEEPALIVE = true;
    const DEFAULT_TIMEOUT = 0;

    const LOG_TRUNCATION_LEN = 30;

    private $redis = null;

    public function __construct($config)
    {
        if (!is_array($config) ||
            (array_key_exists('port', $config) && (!is_numeric($config['port']) ||
                    intval($config['port']) <= 0 || intval($config['port']) > 65535)) ||
            (array_key_exists('index', $config) && (!is_numeric($config['index']) ||
                    intval($config['index']) < 0 || intval($config['index']) > 15)) ||
            (array_key_exists('timeout', $config) && (!is_numeric($config['timeout']) ||
                    intval($config['timeout']) < 0))
        ) {
            throw new \InvalidArgumentException();
        }

        $ip = array_key_exists('ip', $config) ?
            $config['ip'] : self::DEFAULT_IP;

        $retry = false;
        $oriConfig = $ip;
        if (is_array($ip)) {
            if (count($ip) > 1) {
                $retry = true;
            }
            
            $tmpIdx = array_rand($ip);
            $ip = $ip[$tmpIdx];
            unset($oriConfig[$tmpIdx]);
        }

        $port = array_key_exists('port', $config) ?
            intval($config['port']) : self::DEFAULT_PORT;
        $index = array_key_exists('index', $config) ?
            intval($config['index']) : self::DEFAULT_INDEX;
        $keepalive = array_key_exists('keepalive', $config) ?
            (bool)$config['keepalive'] : self::DEFAULT_KEEPALIVE;
        $timeout = array_key_exists('timeout', $config) ?
            doubleval($config['timeout']) / 1000 : self::DEFAULT_TIMEOUT;

        $instance = new \Redis();
        $connectSucceeded = false;
        if ($keepalive) {
            $connectSucceeded = $instance->pconnect($ip, $port, $timeout);
        } else {
            $connectSucceeded = $instance->connect($ip, $port, $timeout);
        }
        
        if (!$connectSucceeded || !$instance->select($index)) {
            if ($retry) {
                $tmpIdx = array_rand($oriConfig);
                $ip = $oriConfig[$tmpIdx];
                if ($keepalive) {
                    $connectSucceeded = $instance->pconnect ( $ip, $port, $timeout );
                } else {
                    $connectSucceeded = $instance->connect ( $ip, $port, $timeout );
                }
                
                if (!$connectSucceeded || !$instance->select($index)) {
                    self::logConnectionError($config);
                    throw new \RedisException();
                }
            } else {
                self::logConnectionError($config);
                throw new \RedisException();
            }
        }

        $this->redis = $instance;
        return;
    }

    /**
     * 魔术方法，会自动调用cache 对应的方法
     * @param $method
     * @param $arguments
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        if (!method_exists($this->redis, $method)) {
            throw new \BadFunctionCallException('Redis has not method:' . $method);
        }
        $t1 = microtime(true);
        $return = call_user_func_array(array($this->redis, $method), $arguments);
        $t2 = microtime(true);
        $duration = $t2 - $t1;
        self::logOperation($method, $arguments, $duration, $return);
        return $return;
    }

    private static function logConnectionError($args)
    {
        if (class_exists('Xiaoju\Beatles\Utils\Logger')) {
            $msg = 'redis connection failed';
            $errorNo = -1;
            Logger::fatal($msg, $errorNo, $args);
        }
    }

    /**
     * @param $method
     * @param $arguments
     * @param $duration
     * @param $return
     */
    private static function logOperation($method, $arguments, $duration, $return)
    {
        $params['cost'] = $duration;

        $operValue = implode(' ', $arguments);
        $length = strlen($operValue);
        if ($length > self::LOG_TRUNCATION_LEN) {
            $operValue = substr($operValue, 0, self::LOG_TRUNCATION_LEN) . '...';
        }
        $params['operName'] = $method;
        $params['operValue'] = $operValue;
        $params['operLength'] = $length;

        if (!empty($return)) {
            $returnValue = strval($return);
            $length = strlen($returnValue);
            if ($length > self::LOG_TRUNCATION_LEN) {
                $returnValue = substr($returnValue, 0, self::LOG_TRUNCATION_LEN) . '...';
            }
            $params['resultValue'] = $returnValue;
            $params['resultLength'] = $length;
        }

        if (class_exists('Xiaoju\Beatles\Utils\Logger')) {
            Logger::warning('redis operation', 0, $params);
        }
    }
}

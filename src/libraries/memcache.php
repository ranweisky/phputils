<?php
namespace Xiaoju\Beatles\Utils;

/**
 * Created by PhpStorm.
 *
 * memecache 操作
 * User: xingmin
 * Date: 2014/12/30
 * Time: 14:32
 */

class Memcache
{
    const DEFAULT_IP = '127.0.0.1';
    const DEFAULT_PORT = 11211;
    const INVALIDATE_ARGUMENTS = 'invalidate arguments';
    const CONNECT_FAIL = 101;

    const LOG_TRUNCATION_LEN = 30;

    private $mem = null;

    public function __construct($servers = array(), $persistentId = false)
    {
        if (!is_array($servers)) {
            throw new \InvalidArgumentException(self::INVALIDATE_ARGUMENTS . ':servers');
        }

        if (!is_bool($persistentId)) {
            throw new \InvalidArgumentException(self::INVALIDATE_ARGUMENTS . ':persistentId');
        }

        foreach ($servers as $serverName => $config) {
            if (!is_array($config) ||
                (array_key_exists('port', $config) && (!is_numeric($config['port']) ||
                        intval($config['port']) <= 0 || intval($config['port']) > 65535))
            ) {
                throw new \InvalidArgumentException(self::INVALIDATE_ARGUMENTS . ':servers');
            }
            $config['ip'] = array_key_exists('ip', $config) ?
                $config['ip'] : self::DEFAULT_IP;
            $config['port'] = array_key_exists('port', $config) ?
                $config['port'] : self::DEFAULT_PORT;
        }

        $conn = false;
        if ($persistentId === false) {
            $this->mem = new \Memcached();
        } else {
            $this->mem = new \Memcached($persistentId);
        }

        $conn = $this->mem->addServers($servers);

        if (!$conn) {
            //连接不上 记录日志 抛出错误
            self::log('fatal', 'memcache connect server fail', self::CONNECT_FAIL, $servers);
            throw new \Exception('connect memcache server fail');
        }
    }

    /**
     * 魔术方法，会自动调用memcached 对应的方法
     * @param $method
     * @param $arguments
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        if (!method_exists($this->mem, $method)) {
            throw new \BadFunctionCallException('Memcached has not method:' . $method);
        }
        $t1 = microtime(true);
        $return = call_user_func_array(array($this->mem, $method), $arguments);
        $t2 = microtime(true);
        $duration = $t2 - $t1;
        $this->logOperation($method, $arguments, $return, $duration);
        return $return;
    }

    private static function log($type, $msg, $errorNo, $params)
    {
        if (class_exists('Xiaoju\Beatles\Utils\Logger')) {
            switch ($type) {
                case 'debug':
                    Logger::debug($msg, $errorNo, $params);
                    break;
                case 'trace':
                    Logger::trace($msg, $errorNo, $params);
                    break;
                case 'notice':
                    Logger::notice($msg, $errorNo, $params);
                    break;
                case 'warning':
                    Logger::warning($msg, $errorNo, $params);
                    break;
                case 'fatal':
                    Logger::fatal($msg, $errorNo, $params);
                    break;
            }
        }
    }

    /**
     * @param $method
     * @param $arguments
     * @param $return
     * @param $duration
     */
    private function logOperation($method, $arguments, $return, $duration)
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
        self::log('warning', 'memcache operation', 0, $params);
    }
}

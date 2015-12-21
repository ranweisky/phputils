<?php

namespace Xiaoju\Beatles\Utils\Utest;

use Xiaoju\Beatles\Utils as Utils;

require_once dirname(__FILE__) . '/../../src/libraries/redis.php';
require_once dirname(__FILE__) . '/../../src/helper/logger.php';

error_reporting(E_ALL);

// @codingStandardsIgnoreLine two class statements in one file
class RedisTests extends \PHPUnit_Framework_TestSuite
{
    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite();
        $suite->setName('Xiaoju kozacy redis lib tests');
        $suite->addTestSuite('Xiaoju\Beatles\Utils\Utest\RedisTest');
        return $suite;
    }
}

// @codingStandardsIgnoreLine two class statements in one file
class RedisTest extends \PHPUnit_Framework_TestCase
{
    public function __construct()
    {
    }

    /*
     * @requires redis
     */
    public function testNormalCreation()
    {
        $config = parse_ini_file(dirname(__FILE__) . '/../testdata/libraries/redis.ini', true);
        $exceptionCaught = false;
        try {
            $redis = new Utils\Redis($config['TestRedis']);
            $key = 'TEST_KEY_' . time();
            $value = 'a long long long long long long long long long long test one';
            $redis->setex($key, 10000, $value);
            $actualValue = $redis->get($key);
            $this->assertTrue(strcmp($value, $actualValue) == 0);
            $redis->del($key);
        } catch (\Exception $e) {
            echo 'exception caught: type=' . get_class($e) . '||trace=' . $e->getTraceAsString() .
                '||msg=' . $e->getMessage();
            $exceptionCaught = true;
        }
        $this->assertFalse($exceptionCaught);
    }

    /*
     * @requires redis
     */
    public function testInvalidIndexCreation()
    {
        $config = parse_ini_file(dirname(__FILE__) . '/../testdata/libraries/redis.ini', true);
        $exceptionCaught = false;
        try {
            $redis = new Utils\Redis($config['InvalidIndexRedis']);
            $key = 'TEST_KEY_' . time();
            $value = 'a test one';
            $redis->setex($key, 10000, $value);
            $actualValue = $redis->get($key);
            $this->assertTrue(strcmp($value, $actualValue) == 0);
        } catch (\Exception $e) {
            $exceptionCaught = true;
        }
        $this->assertTrue($exceptionCaught);
    }

    /*
     * @requires redis
     */
    public function testInvalidIpCreation()
    {
        $config = parse_ini_file(dirname(__FILE__) . '/../testdata/libraries/redis.ini', true);
        //var_dump($config);
        $exceptionCaught = false;
        try {
            $redis = new Utils\Redis($config['InvalidIpRedis']);
            $key = 'TEST_KEY_' . time();
            $value = 'a test one';
            $redis->setex($key, 10000, $value);
            $actualValue = $redis->get($key);
            $this->assertTrue(strcmp($value, $actualValue) == 0);
        } catch (\Exception $e) {
            $exceptionCaught = true;
        }
        $this->assertTrue($exceptionCaught);
    }
}

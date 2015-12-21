<?php
namespace Xiaoju\Beatles\Utils\Utest;

use Xiaoju\Beatles\Utils as Utils;

require_once dirname(__FILE__) . '/../../src/libraries/memcache.php';
require_once dirname(__FILE__) . '/../../src/helper/logger.php';

error_reporting(E_ALL);

// @codingStandardsIgnoreLine two class statements in one file
class MemcacheTests extends \PHPUnit_Framework_TestSuite
{
    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite();
        $suite->setName('Xiaoju kozacy validator helper tests');
        $suite->addTestSuite('Xiaoju\Beatles\Utils\Utest\MemcacheTest');
        return $suite;
    }
}

// @codingStandardsIgnoreLine two class statements in one file
class MemcacheTest extends \PHPUnit_Framework_TestCase
{
    private $server = array('127.0.0.1', 11211);


    public function __construct()
    {
        if (!is_dir('./log')) {
            mkdir('./log');
        }
    }

    public function testConnect()
    {
        $config = parse_ini_file(dirname(__FILE__) . '/../testdata/libraries/memcache.ini', true);
        try {
            $mem = new Utils\Memcache($config);
            $this->assertTrue(true, 'Test database connect');
        } catch (\Exception $e) {
            echo $e->getMessage();
            $this->assertTrue(false, 'Test database connect');
        }

        $servers = array(
            array('127.0.0.1', 123),
            array('127.0.0.1', 11211)
        );
        try {
            $mem = new Utils\Memcache($servers);
            $this->assertTrue(true, 'test connection failed');
        } catch (\Exception $e) {
            echo $e->getMessage();
            $this->assertTrue(false, 'test connection failed');
        }
    }


    public function testSet()
    {
        $config = parse_ini_file(dirname(__FILE__) . '/../testdata/libraries/memcache.ini', true);
        try {
            $mem = new Utils\Memcache($config);
            $key = 'test:' . time();
            $value = 'such a long long long value value value ....';
            $re = $mem->Set($key, $value);
            $this->assertTrue($re, 'test set failed');
            $re = $mem->Get($key);
            $this->assertTrue(strcmp($re, $value) == 0, 'test get failed');
        } catch (\Exception $e) {
            echo $e->getMessage();
            $this->assertTrue(false, 'test failed');
        }
    }
}

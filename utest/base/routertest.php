<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2014/12/31
 * Time: 17:55
 */

namespace Xiaoju\Beatles\Utils\Utest;

use \Xiaoju\Beatles\Framework as Framework;

defined('FRAMEPATH') or define('FRAMEPATH', dirname(__FILE__) . '/../../src/');

require_once dirname(__FILE__) . '/../../src/base/router.php';
require_once dirname(__FILE__) . '/../../src/base/uri.php';
require_once dirname(__FILE__) . '/../testdata/base/controller/driver/order/fail.php';
require_once dirname(__FILE__) . '/../testdata/base/controller/driver/index.php';
require_once dirname(__FILE__) . '/../testdata/base/controller/index.php';

global $appNameSpace;
$appNameSpace = 'Xiaoju\Beatles\App\Babypig\Base';

class RouterTests extends \PHPUnit_Framework_TestSuite
{
    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite();
        $suite->setName('Xiaoju kozacy router tests');
        $suite->addTestSuite('Xiaoju\Beatles\Utils\Utest\RouterTest');
        return $suite;
    }
}

// @codingStandardsIgnoreLine two class statements in one file
class RouterTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {

    }

    protected function tearDown()
    {
    }

    public function testPathExist()
    {
        $uri = '/driver/order/fail?oid=1234&token=0000';
        $router = new Framework\Base\Router($uri);
        $router->setRoute();
        $params = array('get' => array('oid' => 1234));
        $output = $router->run($params);
        $this->assertEquals(
            $output,
            'driver/order/fail',
            'actual result differs from expected: ' . var_export($output, true)
        );
    }

    public function testPrefixPathExist()
    {
        $uri = '/babypig/driver/order/fail?oid=1234&token=0000';
        $router = new Framework\Base\Router($uri, array('/babypig/(.+)' => '$1'));
        $router->setRoute();
        $params = array('get' => array('oid' => 1234));
        $output = $router->run($params);
        $this->assertEquals(
            $output,
            'driver/order/fail',
            'actual result differs from expected: ' . var_export($output, true)
        );
    }

    public function testPrefixPathExist2()
    {
        $uri = '/babypig/middle/driver/order/fail?oid=1234&token=0000';
        $router = new Framework\Base\Router($uri, array('/babypig/middle/(.+)' => '$1'));
        $router->setRoute();
        $params = array('get' => array('oid' => 1234));
        $output = $router->run($params);
        $this->assertEquals(
            $output,
            'driver/order/fail',
            'actual result differs from expected: ' . var_export($output, true)
        );
    }

    public function testMethodNotExist()
    {
        $uri = '/driver/order/notexist?oid=1234&token=0000';
        $router = new Framework\Base\Router($uri);
        $router->setRoute();
        $params = array('get' => array('oid' => 1234));
        $output = $router->run($params);
        $this->assertEquals(
            $output,
            'driver/index',
            'actual result differs from expected: ' . var_export($output, true)
        );
    }

    public function testDefaultController1()
    {
        $uri = '/driver/passenger/?oid=1234&token=0000';
        $router = new Framework\Base\Router($uri);
        $router->setRoute();
        $params = array('get' => array('oid' => 1234));
        $output = $router->run($params);
        $this->assertEquals(
            $output,
            'driver/index',
            'actual result differs from expected: ' . var_export($output, true)
        );
    }

    public function testDefaultController2()
    {
        $uri = '/passenger/?oid=1234&token=0000';
        $router = new Framework\Base\Router($uri);
        $router->setRoute();
        $params = array('get' => array('oid' => 1234));
        $output = $router->run($params);
        $this->assertEquals(
            $output,
            'index',
            'actual result differs from expected: ' . var_export($output, true)
        );
    }
}

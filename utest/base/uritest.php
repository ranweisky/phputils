<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2014/12/31
 * Time: 19:17
 */

namespace Xiaoju\Beatles\Utils\Utest;

use \Xiaoju\Beatles\Framework as Framework;

defined('FRAMEPATH') or define('FRAMEPATH', dirname(__FILE__) . '/../../src/');
require_once dirname(__FILE__) . '/../../src/base/uri.php';

class UriTests extends \PHPUnit_Framework_TestSuite
{
    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite();
        $suite->setName('Xiaoju kozacy uri tests');
        $suite->addTestSuite('Xiaoju\Beatles\Utils\Utest\UriTest');
        return $suite;
    }
}

// @codingStandardsIgnoreLine two class statements in one file
class UriTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {

    }

    protected function tearDown()
    {
    }

    public function testNormalPath()
    {
        $uriPath = '/driver/strive?oid=1234&token=0000';
        $uri = new Framework\Base\Uri($uriPath);
        $output = $uri->explodeSegments();
        $this->assertEquals(
            count($output),
            2,
            'actual result count differs from expected: ' . var_export($output, true)
        );
        $this->assertEquals(
            $output[0],
            'driver',
            'actual result path differs from expected: ' . var_export($output, true)
        );
        $this->assertEquals(
            $output[1],
            'strive',
            'actual result path differs from expected: ' . var_export($output, true)
        );
    }

    public function testSpecialPath()
    {
        $uriPath = '/driver/%28login?oid=1234&token=0000';
        $uri = new Framework\Base\Uri($uriPath);
        $output = $uri->explodeSegments();
        $this->assertEquals(
            count($output),
            2,
            'actual result count differs from expected: ' . var_export($output, true)
        );
        $this->assertEquals(
            $output[0],
            'driver',
            'actual result path differs from expected: ' . var_export($output, true)
        );
        $this->assertEquals(
            $output[1],
            '&#40;login',
            'actual result path differs from expected: ' . var_export($output, true)
        );
    }
}

<?php

namespace Xiaoju\Beatles\Utils\Utest;

use Xiaoju\Beatles\Utils as Utils;

require_once dirname(__FILE__) . '/../../src/helper/authentication.php';

error_reporting(E_ALL);

// @codingStandardsIgnoreLine two class statements in one file
class AuthenticationTests extends \PHPUnit_Framework_TestSuite
{
    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite();
        $suite->setName('Xiaoju kozacy authentication helper tests');
        $suite->addTestSuite('Xiaoju\Beatles\Utils\Utest\AuthenticationTest');
        return $suite;
    }
}

// @codingStandardsIgnoreLine two class statements in one file
class AuthenticationTest extends \PHPUnit_Framework_TestCase
{
    public function __construct()
    {
    }

    public function testAuthentication()
    {
        $args = array('c' => 11, 'a' => 'b', '1' => '82', 'xxx' => 'c');
        $key = 'Its a strange key';
        $sign = Utils\Authentication::createSign($key, $args);
        $this->assertTrue(is_string($sign), 'no invalid sign generated');

        $verifyPassedResult = Utils\Authentication::verify($key, $args, $sign);
        $this->assertTrue($verifyPassedResult, 'verify failed, expected passed');

        unset($args['c']);
        $verifyFailedResult = Utils\Authentication::verify($key, $args, $sign);
        $this->assertFalse($verifyFailedResult, 'verify passed, expected failed');
    }
}

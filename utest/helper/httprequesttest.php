<?php

namespace Xiaoju\Beatles\Utils\Utest;

use Xiaoju\Beatles\Utils as Utils;

require_once dirname(__FILE__) . '/../../src/helper/logger.php';
require_once dirname(__FILE__) . '/../../src/helper/httprequest.php';

error_reporting(E_ALL);

// @codingStandardsIgnoreLine two class statements in one file
class HttpRequestTests extends \PHPUnit_Framework_TestSuite
{
    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite();
        $suite->setName('Xiaoju kozacy http request helper tests');
        $suite->addTestSuite('Xiaoju\Beatles\Utils\Utest\HttpRequestTest');
        return $suite;
    }
}

// @codingStandardsIgnoreLine two class statements in one file
class HttpRequestTest extends \PHPUnit_Framework_TestCase
{
    public function __construct()
    {
    }

    public function testGetSuccess()
    {
        $successResult = null;
        $successUrl = 'http://www.xiaojukeji.com';
        $caseName = 'Test Get in success mode, ';
        $successTimeout = 3000;
        $triggerException = false;
        try {
            $successResult = Utils\HttpRequest::get($successUrl, 'test', $successTimeout);
        } catch (Exception $e) {
            $triggerException = true;
        }
        $this->assertFalse($triggerException, $caseName . 'triggering exception');
        $this->assertTrue(
            is_string($successResult) && !empty($successResult),
            $caseName . 'no result returned'
        );
    }

    /**
     * @expectedException Exception
     */
    public function testGetNotExist()
    {
        // not exist url
        $notExistResult = null;
        $notExistUrl = 'http://127.0.0.d/neversayimexistinthisworld.com';
        $caseName = 'Test Get in not exist url mode, ';
        $triggerException = false;

        try {
            $notExistResult = Utils\HttpRequest::get($notExistUrl);
        } catch (Exception $e) {
            $triggerException = true;
        }
        $this->assertTrue($triggerException, $caseName . 'not triggering exception');
        $this->assertTrue(empty($notExistResult), $caseName . 'unexpected result returned');
    }

    /**
     * @expectedException Exception
     */
    public function testGetTimeout()
    {
        // timeout url
        $timeoutResult = null;
        $timeoutUrl = 'http://www.xiaojukeji.com';
        $caseName = 'Test Get in timeout mode, ';
        $triggerException = false;
        $extremelyShortTimeout = 1;
        try {
            $timeoutResult = Utils\HttpRequest::get($timeoutUrl, 'null', $extremelyShortTimeout);
        } catch (Exception $e) {
            $triggerException = true;
        }
        $this->assertTrue($triggerException, $caseName . 'not triggering exception');
        $this->assertTrue(empty($timeoutResult), $caseName . 'unexpected result returned');
    }

    public function testPostSuccess()
    {
        $successResult = null;
        $successUrl = 'http://www.xiaojukeji.com';
        $caseName = 'Test Post in success mode, ';
        $successTimeout = 3000;
        $triggerException = false;
        $args = array('a' => 'a', 'b' => 'b');
        try {
            $successResult = Utils\HttpRequest::post($successUrl, $args, 'testpost', $successTimeout);
        } catch (Exception $e) {
            $triggerException = true;
        }
        $this->assertFalse($triggerException, $caseName . 'triggering exception');
        $this->assertTrue(
            is_string($successResult) && !empty($successResult),
            $caseName . 'no result returned'
        );
    }

    /**
     * @expectedException Exception
     */
    public function testPostNotExist()
    {
        // not exist url
        $notExistResult = null;
        $notExistUrl = 'http://127.0.0.d/neversayimexistinthisworld.com';
        $caseName = 'Test Post in not exist url mode, ';
        $triggerException = false;
        $args = array('a' => 'a', 'b' => 'b');
        try {
            $notExistResult = Utils\HttpRequest::post($notExistUrl, $args, 'testpost');
        } catch (Exception $e) {
            $triggerException = true;
        }
        $this->assertTrue($triggerException, $caseName . 'not triggering exception');
        $this->assertTrue(empty($notExistResult), $caseName . 'unexpected result returned');
    }

    /**
     * @expectedException Exception
     */
    public function testPostTimeout()
    {
        // timeout url
        $timeoutResult = null;
        $timeoutUrl = 'http://www.xiaojukeji.com';
        $caseName = 'Test Post in timeout mode, ';
        $triggerException = false;
        $extremelyShortTimeout = 1;
        $args = array('a' => 'a', 'b' => 'b');
        try {
            $timeoutResult = Utils\HttpRequest::post($timeoutUrl, $args, 'null', $extremelyShortTimeout);
        } catch (Exception $e) {
            $triggerException = true;
        }
        $this->assertTrue($triggerException, $caseName . 'not triggering exception');
        $this->assertTrue(empty($timeoutResult), $caseName . 'unexpected result returned');
    }
}

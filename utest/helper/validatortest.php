<?php

namespace Xiaoju\Beatles\Utils\Utest;

use Xiaoju\Beatles\Utils as Utils;

require_once dirname(__FILE__) . '/../../src/helper/validator.php';

error_reporting(E_ALL);

// @codingStandardsIgnoreLine two class statements in one file
class ValidatorTests extends \PHPUnit_Framework_TestSuite
{
    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite();
        $suite->setName('Xiaoju kozacy validator helper tests');
        $suite->addTestSuite('Xiaoju\Beatles\Utils\Utest\ValidatorTest');
        return $suite;
    }
}

/**
 * Class ValidatorTester
 * @package Xiaoju\Beatles\Utils\Utest
 *
 */
// @codingStandardsIgnoreLine two class statements in one file
class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    private $testcases = array(
        'Digit' => array('0012321', '028736166212123123123'),
        'Int' => array('12332', '+1234', '-2753', '129732631623612827371236'),
        'XDigit' => array('F73E', '237D'),
        'Float' => array('0.2837123', '-2837.223123', '+18273'),
        'Ip' => array('127.2.3.21', '255.255.255.255'),
        'Alpha' => array('aocijfe', 'icjfloqpweXIJFEI', '中文'),
        'DigitWithAlpha' => array('128fjiwe', '82jk83'),
        'Nonspace' => array('aa aabc', 'abczcd  ss', 'asdi;'),
        'Cellphone' => array('12345678901', '1234567890'),
        'Version' => array('1.1.2a', '2.2.3', '2a'),
    );

    public function testDigit()
    {
        $testResults = array(
            'Digit' => array(true, true),
            'Int' => array(true, false, false, true),
            'XDigit' => array(false, false),
            'Float' => array(false, false, false),
            'Ip' => array(false, false),
            'Alpha' => array(false, false, false),
            'DigitWithAlpha' => array(false, false),
            'Nonspace' => array(false, false, false),
            'Cellphone' => array(true, true),
            'Version' => array(false, false, false),
        );
        $this->doTest($testResults, 'isDigit');
    }

    public function testInt()
    {
        $testResults = array(
            'Digit' => array(true, true),
            'Int' => array(true, false, true, true),
            'XDigit' => array(false, false),
            'Float' => array(false, false, false),
            'Ip' => array(false, false),
            'Alpha' => array(false, false, false),
            'DigitWithAlpha' => array(false, false),
            'Nonspace' => array(false, false, false),
            'Cellphone' => array(true, true),
            'Version' => array(false, false, false),
        );

        $this->doTest($testResults, 'isInt');
    }

    public function testXDigit()
    {
        $testResults = array(
            'Digit' => array(true, true),
            'Int' => array(true, false, false, true),
            'XDigit' => array(true, true),
            'Float' => array(false, false, false),
            'Ip' => array(false, false),
            'Alpha' => array(false, false, false),
            'DigitWithAlpha' => array(false, false),
            'Nonspace' => array(false, false, false),
            'Cellphone' => array(true, true),
            'Version' => array(false, false, true),
        );

        $this->doTest($testResults, 'isXDigit');
    }

    public function testFloat()
    {
        $testResults = array(
            'Digit' => array(true, true),
            'Int' => array(true, false, true, true),
            'XDigit' => array(false, false),
            'Float' => array(true, true, false),
            'Ip' => array(false, false),
            'Alpha' => array(false, false, false),
            'DigitWithAlpha' => array(false, false),
            'Nonspace' => array(false, false, false),
            'Cellphone' => array(true, true),
            'Version' => array(false, false, false),
        );

        $this->doTest($testResults, 'isFloat');
    }

    public function testIp()
    {
        $testResults = array(
            'Digit' => array(false, false),
            'Int' => array(false, false, false, false),
            'XDigit' => array(false, false),
            'Float' => array(false, false, false),
            'Ip' => array(true, true),
            'Alpha' => array(false, false, false),
            'DigitWithAlpha' => array(false, false),
            'Nonspace' => array(false, false, false),
            'Cellphone' => array(false, false),
            'Version' => array(false, false, false),
        );

        $this->doTest($testResults, 'isIp');
    }

    public function testAlpha()
    {
        $testResults = array(
            'Digit' => array(false, false),
            'Int' => array(false, false, false, false),
            'XDigit' => array(false, false),
            'Float' => array(false, false, false),
            'Ip' => array(false, false),
            'Alpha' => array(true, true, false),
            'DigitWithAlpha' => array(false, false),
            'Nonspace' => array(false, false, false),
            'Cellphone' => array(false, false),
            'Version' => array(false, false, false),
        );

        $this->doTest($testResults, 'isAlpha');
    }

    public function testDigitWithAlpha()
    {
        $testResults = array(
            'Digit' => array(true, true),
            'Int' => array(true, false, false, true),
            'XDigit' => array(true, true),
            'Float' => array(false, false, false),
            'Ip' => array(false, false),
            'Alpha' => array(true, true, false),
            'DigitWithAlpha' => array(true, true),
            'Nonspace' => array(false, false, false),
            'Cellphone' => array(true, true),
            'Version' => array(false, false, true),
        );

        $this->doTest($testResults, 'isDigitWithAlpha');
    }

    public function testNonspace()
    {
        $testResults = array(
            'Digit' => array(true, true),
            'Int' => array(true, true, true, true),
            'XDigit' => array(true, true),
            'Float' => array(true, true, true),
            'Ip' => array(true, true),
            'Alpha' => array(true, true, true),
            'DigitWithAlpha' => array(true, true),
            'Nonspace' => array(false, false, true),
            'Cellphone' => array(true, true),
            'Version' => array(true, true, true),
        );

        $this->doTest($testResults, 'isNonspace');
    }

    public function testCellphone()
    {
        $testResults = array(
            'Digit' => array(false, false),
            'Int' => array(false, false, false, false),
            'XDigit' => array(false, false),
            'Float' => array(false, false, false),
            'Ip' => array(false, false),
            'Alpha' => array(false, false, false),
            'DigitWithAlpha' => array(false, false),
            'Nonspace' => array(false, false, false),
            'Cellphone' => array(true, false),
            'Version' => array(false, false, false),
        );

        $this->doTest($testResults, 'isCellphone');
    }

    public function testVersion()
    {
        $testResults = array(
            'Digit' => array(false, false),
            'Int' => array(false, false, false, false),
            'XDigit' => array(false, false),
            'Float' => array(false, false, false),
            'Ip' => array(false, false),
            'Alpha' => array(false, false, false),
            'DigitWithAlpha' => array(false, false),
            'Nonspace' => array(false, false, false),
            'Cellphone' => array(false, false),
            'Version' => array(true, true, false),
        );

        $this->doTest($testResults, 'isVersion');
    }

    /**
     * @param $testResults
     * @param $func
     */
    public function doTest($testResults, $func)
    {
        foreach ($this->testcases as $name => $cases) {
            for ($i = 0; $i < count($cases); ++$i) {
                $result = Utils\Validator::$func($cases[$i]);
                $caseName = 'Testing ' . $name . ' with case[' . $cases[$i] . '] ... ' .
                    'expected=' . $testResults[$name][$i] . ' actual=' . $result;
                $this->assertTrue($result === $testResults[$name][$i], $caseName);
            }
        }
    }
}

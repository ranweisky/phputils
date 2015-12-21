<?php

namespace Xiaoju\Beatles\Utils\Utest;

use Xiaoju\Beatles\Utils as Utils;

require_once dirname(__FILE__) . '/../../src/helper/argumentvalidator.php';

error_reporting(E_ALL);

// @codingStandardsIgnoreLine two class statements in one file
class ArgumentValidatorTests extends \PHPUnit_Framework_TestSuite
{
    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite();
        $suite->setName('Xiaoju kozacy argumentvalidator helper tests');
        $suite->addTestSuite('Xiaoju\Beatles\Utils\Utest\ArgumentValidatorTest');
        return $suite;
    }
}

// @codingStandardsIgnoreLine two class statements in one file
class ArgumentValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function testType()
    {
        $config = parse_ini_file(dirname(__FILE__) . '/../testdata/helper/argumentvalidator.ini', true);
        $av = new Utils\ArgumentValidator();
        $get = array(
            'typeArg' => "10",
        );
        $this->validateTest($av, $get, $config, false);

        $get = array(
            'typeArg' => 'aaa',
        );
        $this->validateTest($av, $get, $config, true);
    }

    public function testNotZero()
    {
        $config = parse_ini_file(dirname(__FILE__) . '/../testdata/helper/argumentvalidator.ini', true);
        $av = new Utils\ArgumentValidator();
        static $testKey = 'notZeroArg';

        $get = array(
            $testKey => "10",
        );
        $this->validateTest($av, $get, $config, false);

        $get = array(
            $testKey => '0',
        );
        $this->validateTest($av, $get, $config, true);

        $get = array(
            $testKey => 'aaa',
        );
        $this->validateTest($av, $get, $config, true);
    }

    public function testNotBlank()
    {
        $config = parse_ini_file(dirname(__FILE__) . '/../testdata/helper/argumentvalidator.ini', true);
        $av = new Utils\ArgumentValidator();
        static $testKey = 'notBlankArg';

        $get = array(
            $testKey => "10",
        );
        $this->validateTest($av, $get, $config, false);

        $get = array(
            $testKey => 'aaa',
        );
        $this->validateTest($av, $get, $config, false);

        $get = array(
            $testKey => '',
        );
        $this->validateTest($av, $get, $config, true);
    }

    public function testLowerBound()
    {
        $config = parse_ini_file(dirname(__FILE__) . '/../testdata/helper/argumentvalidator.ini', true);
        $av = new Utils\ArgumentValidator();
        static $testKey = 'lowerBoundArg';

        $get = array(
            $testKey => "10",
        );
        $this->validateTest($av, $get, $config, false);

        $get = array(
            $testKey => '1',
        );
        $this->validateTest($av, $get, $config, false);

        $get = array(
            $testKey => '0',
        );
        $this->validateTest($av, $get, $config, true);

        $get = array(
            $testKey => 'aaa',
        );
        $this->validateTest($av, $get, $config, true);
    }

    public function testUpperBound()
    {
        $config = parse_ini_file(dirname(__FILE__) . '/../testdata/helper/argumentvalidator.ini', true);
        $av = new Utils\ArgumentValidator();
        static $testKey = 'upperBoundArg';

        $get = array(
            $testKey => "10",
        );
        $this->validateTest($av, $get, $config, false);

        $get = array(
            $testKey => '1',
        );
        $this->validateTest($av, $get, $config, false);

        $get = array(
            $testKey => '11',
        );
        $this->validateTest($av, $get, $config, true);

        $get = array(
            $testKey => 'aaa',
        );
        $this->validateTest($av, $get, $config, true);
    }

    public function testMinLength()
    {
        $config = parse_ini_file(dirname(__FILE__) . '/../testdata/helper/argumentvalidator.ini', true);
        $av = new Utils\ArgumentValidator();
        static $testKey = 'minLengthArg';

        $get = array(
            $testKey => 'aaaaa',
        );
        $this->validateTest($av, $get, $config, false);

        $get = array(
            $testKey => 'aa',
        );
        $this->validateTest($av, $get, $config, true);

        $get = array(
            $testKey => '11',
        );
        $this->validateTest($av, $get, $config, true);

        $get = array(
            $testKey => '11111',
        );
        $this->validateTest($av, $get, $config, false);
    }

    public function testMaxLength()
    {
        $config = parse_ini_file(dirname(__FILE__) . '/../testdata/helper/argumentvalidator.ini', true);
        $av = new Utils\ArgumentValidator();
        static $testKey = 'maxLengthArg';

        $get = array(
            $testKey => 'aaaaa',
        );
        $this->validateTest($av, $get, $config, false);

        $get = array(
            $testKey => 'aa',
        );
        $this->validateTest($av, $get, $config, false);

        $get = array(
            $testKey => '11',
        );
        $this->validateTest($av, $get, $config, false);

        $get = array(
            $testKey => '1111111',
        );
        $this->validateTest($av, $get, $config, true);
    }

    public function testRegex()
    {
        $config = parse_ini_file(dirname(__FILE__) . '/../testdata/helper/argumentvalidator.ini', true);
        $av = new Utils\ArgumentValidator();
        static $testKey = 'regexArg';

        $get = array(
            $testKey => 'aaaaa',
        );
        $this->validateTest($av, $get, $config, false);

        $get = array(
            $testKey => 'aa',
        );
        $this->validateTest($av, $get, $config, false);

        $get = array(
            $testKey => '11',
        );
        $this->validateTest($av, $get, $config, true);

        $get = array(
            $testKey => '',
        );
        $this->validateTest($av, $get, $config, true);
    }

    public function testMixInt()
    {
        $config = parse_ini_file(dirname(__FILE__) . '/../testdata/helper/argumentvalidator.ini', true);
        $av = new Utils\ArgumentValidator();
        static $testKey = 'mixIntArg';

        $get = array(
            $testKey => '-5',
        );
        $this->validateTest($av, $get, $config, false);

        $get = array(
            $testKey => '10',
        );
        $this->validateTest($av, $get, $config, false);

        $get = array(
            $testKey => '1.2',
        );
        $this->validateTest($av, $get, $config, true);

        $get = array(
            $testKey => '0',
        );
        $this->validateTest($av, $get, $config, true);

        $get = array(
            $testKey => '14',
        );
        $this->validateTest($av, $get, $config, true);

        $get = array(
            $testKey => '-5.3',
        );
        $this->validateTest($av, $get, $config, true);

        $get = array(
            $testKey => 'aaaa',
        );
        $this->validateTest($av, $get, $config, true);

        $get = array(
            $testKey => '',
        );
        $this->validateTest($av, $get, $config, true);
    }

    public function testMixString()
    {
        $config = parse_ini_file(dirname(__FILE__) . '/../testdata/helper/argumentvalidator.ini', true);
        $av = new Utils\ArgumentValidator();
        static $testKey = 'mixStringArg';

        $get = array(
            $testKey => 'zeus',
        );
        $this->validateTest($av, $get, $config, false);

        $get = array(
            $testKey => 'ac',
        );
        $this->validateTest($av, $get, $config, false);

        $get = array(
            $testKey => 'ars',
        );
        $this->validateTest($av, $get, $config, false);

        $get = array(
            $testKey => '0000',
        );
        $this->validateTest($av, $get, $config, true);

        $get = array(
            $testKey => 'a',
        );
        $this->validateTest($av, $get, $config, true);

        $get = array(
            $testKey => '',
        );
        $this->validateTest($av, $get, $config, true);
    }

    /**
     * @param $av
     * @param $get
     * @param $config
     * @return array
     */
    private function validateTest($av, $get, $config, $invalid)
    {
        $exceptionCaught = false;
        $msg = '';
        $trace = '';
        try {
            $av->validateArgs($get, $config);
        } catch (\InvalidArgumentException $e) {
            $exceptionCaught = true;
            $msg = $e->getMessage();
            $trace = $e->getTraceAsString();
        }
        $this->assertTrue($exceptionCaught == $invalid, 'validation shall be ok with arg=' .
            json_encode($get) . ' exception=' . $msg . ' trace=' . $trace);
    }
}

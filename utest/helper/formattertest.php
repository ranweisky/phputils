<?php

namespace Xiaoju\Beatles\Utils\Utest;

use \Xiaoju\Beatles\Utils as Utils;

require_once dirname(__FILE__) . '/../../src/helper/formatter.php';

error_reporting(E_ALL);

// @codingStandardsIgnoreLine two class statements in one file
class FormatterTests extends \PHPUnit_Framework_TestSuite
{
    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite();
        $suite->setName('Xiaoju kozacy formatter helper tests');
        $suite->addTestSuite('Xiaoju\Beatles\Utils\Utest\FormatterTest');
        return $suite;
    }
}

/**
 * Class FormatterTest
 * @package Xiaoju\Beatles\Utils\Utest
 *
 */
// @codingStandardsIgnoreLine two class statements in one file
class FormatterTest extends \PHPUnit_Framework_TestCase
{
    public function testNormal()
    {
        $input = array('a' => 0, 'b' => 'test', array(1,2,3));
        $output = Utils\Formatter::format($input);
        $this->assertEquals(
            $output,
            '{"a":"0","b":"test","0":["1","2","3"]}',
            'actual result count differs from expected: ' . var_export($output, true)
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNull()
    {
        $input = array('a' => 0, 'b' => 'test', array(1,2,null));
        $output = Utils\Formatter::format($input);
    }
}

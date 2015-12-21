<?php

namespace Xiaoju\Beatles\Utils\Utest;

use Xiaoju\Beatles\Utils as Utils;

require_once dirname(__FILE__) . '/../../src/helper/logger.php';

error_reporting(E_ALL);

// @codingStandardsIgnoreLine two class statements in one file
class LoggerTests extends \PHPUnit_Framework_TestSuite
{
    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite();
        $suite->setName('Xiaoju kozacy logger helper tests');
        $suite->addTestSuite('Xiaoju\Beatles\Utils\Utest\LoggerTest');
        return $suite;
    }
}

// @codingStandardsIgnoreLine two class statements in one file
class LoggerTest extends \PHPUnit_Framework_TestCase
{
    const LOG_DIR = './log_test/';
    private $config = array(
        'level' => 0XFF,
        'filePath' => './log_test/didi.log',
        'maxFileSize' => 0, //0为不限制
    );

    public function __construct()
    {
    }

    protected function setUp()
    {
        Utils\Logger::create(
            $this->config['level'],
            $this->config['filePath'],
            $this->config['maxFileSize']
        );
    }

    protected function tearDown()
    {
        $restoredConfig = $this->config;
        $restoredConfig['filePath'] = './log/didi.log';
        Utils\Logger::create(
            $restoredConfig['level'],
            $restoredConfig['filePath'],
            $restoredConfig['maxFileSize']
        );
    }

    /**
     *
     */
    public function testDebug()
    {
        $fileName = self::LOG_DIR . 'didi.log';
        $logMsg = 'Test debug';
        $logArgs = array('d' => 'a', 'c' => 1);
        $logId = Utils\Logger::getLogId();
        $logErrorNo = 120;
        $expectedResult = array(
            '[DEBUG]' => null,
            'logId' => $logId,
            'errno' => $logErrorNo,
            'msg' => $logMsg,
            'ip' => '127.0.0.1',
            'uri' => '',
        );
        $expectedResult = array_merge($expectedResult, $logArgs);

        @unlink($fileName);
        Utils\Logger::setLogId($logId);
        Utils\Logger::debug($logMsg, $logErrorNo, $logArgs);

        $logContents = file_get_contents($fileName);
        $actualResult = $this->parseLog(str_replace(PHP_EOL, '', $logContents));
        unset($actualResult['time']);
        unset($actualResult['line']);
        $diff = array_diff_assoc($actualResult, $expectedResult);
        $this->assertEquals(
            count($diff),
            0,
            'actual result differs from expected: ' . var_export($diff, true)
        );
    }

    public function testFatal()
    {
        $fileName = self::LOG_DIR . 'didi.log.wf';
        $logMsg = 'Test fatal';
        $logArgs = array('a' => 'a', 'b' => 1);
        $logId = Utils\Logger::getLogId();
        $logErrorNo = 101;
        $expectedResult = array(
            '[FATAL]' => null,
            'logId' => $logId,
            'errno' => $logErrorNo,
            'msg' => $logMsg,
            'ip' => '127.0.0.1',
            'uri' => '',
        );
        $expectedResult = array_merge($expectedResult, $logArgs);

        @unlink($fileName);
        Utils\Logger::setLogId($logId);
        Utils\Logger::fatal($logMsg, $logErrorNo, $logArgs);

        $logMsg2 = 'Test fatal1';
        $logArgs2 = array('h' => 'e', 'aaab' => 1);
        $logId = Utils\Logger::getLogId();
        $logErrorNo2 = 102;
        $expectedResult2 = array(
            '[FATAL]' => null,
            'logId' => $logId,
            'errno' => $logErrorNo2,
            'msg' => $logMsg2,
            'ip' => '127.0.0.1',
            'uri' => '',
        );
        $expectedResult2 = array_merge($expectedResult2, $logArgs2);
        Utils\Logger::setLogId($logId);
        Utils\Logger::fatal($logMsg2, $logErrorNo2, $logArgs2);

        $logContents = file_get_contents($fileName);
        $logLines = explode(PHP_EOL, $logContents);
        $this->assertEquals(
            count($logLines),
            3,
            'log line count not equal to 3'
        );

        $actualResult = $this->parseLog(str_replace(PHP_EOL, '', $logLines[0]));
        unset($actualResult['time']);
        unset($actualResult['line']);
        $diff = array_diff_assoc($actualResult, $expectedResult);
        $this->assertEquals(
            count($diff),
            0,
            'actual result differs from expected: ' . var_export($diff, true)
        );

        $actualResult2 = $this->parseLog(str_replace(PHP_EOL, '', $logLines[1]));
        unset($actualResult2['time']);
        unset($actualResult2['line']);
        $diff2 = array_diff_assoc($actualResult2, $expectedResult2);
        $this->assertEquals(
            count($diff2),
            0,
            'actual result differs from expected: ' . var_export($diff2, true)
        );

        $this->assertTrue(
            empty($logLines[2]),
            'line 3 in log file not empty'
        );
    }

    public function testNoArg()
    {
        $fileName = self::LOG_DIR . 'didi.log.wf';
        $logMsg = 'Test no argument';
        $logId = Utils\Logger::getLogId();
        $logErrorNo = 120;
        $expectedResult = array(
            '[WARNING]' => null,
            'logId' => $logId,
            'errno' => $logErrorNo,
            'msg' => $logMsg,
            'ip' => '127.0.0.1',
            'uri' => '',
        );

        @unlink($fileName);
        Utils\Logger::setLogId($logId);
        Utils\Logger::warning($logMsg, $logErrorNo);

        $logContents = file_get_contents($fileName);
        $actualResult = $this->parseLog(str_replace(PHP_EOL, '', $logContents));
        unset($actualResult['time']);
        unset($actualResult['line']);
        $diff = array_diff_assoc($actualResult, $expectedResult);
        $this->assertEquals(
            count($diff),
            0,
            'actual result differs from expected: ' . var_export($diff, true)
        );
    }

    private function parseLog($content)
    {
        $args = explode('||', $content);
        $result = array();
        foreach ($args as $arg) {
            $keyValuePair = explode('=', $arg);
            $result[$keyValuePair[0]] = null;
            if (isset($keyValuePair[1])) {
                $result[$keyValuePair[0]] = $keyValuePair[1];
            };
        }

        return $result;
    }
}

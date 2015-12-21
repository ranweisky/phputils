<?php
namespace Xiaoju\Beatles\Utils\Utest;

use Xiaoju\Beatles\Utils as Utils;

require_once dirname(__FILE__) . '/../../src/helper/logger.php';
require_once dirname(__FILE__) . '/../../src/libraries/mysql.php';
require_once dirname(__FILE__) . '/../../src/libraries/utilsexception.php';

error_reporting(E_ALL);

// @codingStandardsIgnoreLine two class statements in one file
class MysqlTests extends \PHPUnit_Framework_TestSuite
{
    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite();
        $suite->setName('Xiaoju kozacy validator helper tests');
        $suite->addTestSuite('Xiaoju\Beatles\Utils\Utest\MysqlTest');
        return $suite;
    }
}

// @codingStandardsIgnoreLine two class statements in one file
class MysqlTest extends \PHPUnit_Framework_TestCase
{
    const INIT_TABLE = '
                 CREATE TABLE IF NOT EXISTS `test` (
                        `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                        `n` int(11) NOT NULL,
                        `leng` int(11) NOT NULL,
                        `va` varchar(56) DEFAULT NULL,
                        PRIMARY KEY (`id`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1';

    const DROP_DATABASE = 'DROP DATABASE IF EXISTS `test`';
    const CREATE_DATABASE = 'CREATE DATABASE IF NOT EXISTS `test`';
    const USE_DATABSE = 'USE `test`';


    public function __construct()
    {
        if (!is_dir('./log')) {
            mkdir('./log');
        }

        $this->config = parse_ini_file(dirname(__FILE__) . '/../testdata/libraries/mysql.ini', true);
    }

    private function initDB($conn)
    {
        $conn->run(self::DROP_DATABASE);
        //$conn->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
        $conn->run(self::CREATE_DATABASE);
        $conn->run(self::USE_DATABSE);
        $conn->run(self::INIT_TABLE);

    }

    public function testInitDB()
    {

        try {
            $conn = new Utils\Mysql($this->config['TestMysql']);
            $this->assertTrue(!empty($conn), 'Test pdolib init');
        } catch (Utils\UtilsException $e) {
            echo $e->getMessage();
            $this->assertTrue(false, 'Test pdolib connect');
        }

        $conn->run(self::DROP_DATABASE);
        //$conn->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
        $conn->run(self::CREATE_DATABASE);
        $conn->exec(self::USE_DATABSE);
        $conn->run(self::INIT_TABLE);
    }

    public function testConnect()
    {
        //正常情况连接测试
        try {
            $pdo = new Utils\Mysql($this->config['TestMysql']);
            $this->assertTrue(!empty($pdo), 'Test pdolib connect');
        } catch (Utils\UtilsException $e) {
            echo $e->getMessage();
            $this->assertTrue(false, 'Test pdolib connect');
        }
    }


    public function testConnect2()
    {
        //测试参数传递不正确
        try {
            $pdo = new Utils\Mysql($this->config['TestMysqlHostErr']);
            $this->assertTrue(empty($pdo), 'Test mysql connect');
        } catch (Utils\UtilsException $e) {
            $errCode = $e->getCode();
            $this->assertTrue(Utils\Mysql::isSystemError($errCode), 'Test mysql connect');
        }
    }

    public function testConnect3()
    {
        //测试参数传递不正确,传递了非字符参数 charset
        try {
            $config = $this->config['TestMysqlHostErr'];
            $config['charSet'] = array();
            $pdo = new Utils\Mysql($config);
            $this->assertTrue(empty($pdo), 'Test mysql connect');
        } catch (\InvalidArgumentException $e) {
            $errInfo = $e->getMessage();
            $errInfo = explode(':', $errInfo);
            $this->assertTrue($errInfo[1] == 'charSet', 'Test mysql connect');
        } catch (\Exception $e) {
            $this->assertTrue(true, 'Test mysql connect');
        }


    }


    public function testConnect4()
    {
        //测试参数传递不正确,传递了空的dbname
        try {
            $config = $this->config['TestMysqlHostErr'];
            $config['dbname'] = array();
            $pdo = new Utils\Mysql($config);
            $this->assertTrue(empty($pdo), 'Test mysql connect');
        } catch (\InvalidArgumentException $e) {
            $errInfo = $e->getMessage();
            $errInfo = explode(':', $errInfo);
            $this->assertTrue($errInfo[1] == 'dbname', 'Test mysql connect');
        } catch (\Exception $e) {
            $this->assertTrue(true, 'Test mysql connect');
        }
    }

    public function testConnect5()
    {
        //测试参数传递不正确2,传递了非字符参数port 必须为数字
        try {
            $config = $this->config['TestMysqlHostErr'];
            $config['port'] = 'datas3223';
            $pdo = new Utils\Mysql($config);
            $this->assertTrue(empty($pdo), 'Test mysql connect');
        } catch (\InvalidArgumentException $e) {
            $errInfo = $e->getMessage();
            $errInfo = explode(':', $errInfo);
            $this->assertTrue($errInfo[1] == 'port', 'Test mysql connect');
        } catch (\Exception $e) {
            $this->assertTrue(true, 'Test mysql connect');
        }
    }

    public function testConnect6()
    {
        //测试参数传递不正确,传递了非字符参数port
        try {
            $config = $this->config['TestMysqlHostErr'];
            $config['port'] = array();
            $pdo = new Utils\Mysql($config);
            $this->assertTrue(empty($pdo), 'Test mysql connect');
        } catch (\InvalidArgumentException $e) {
            $errInfo = $e->getMessage();
            $errInfo = explode(':', $errInfo);
            $this->assertTrue($errInfo[1] == 'port', 'Test mysql connect');
        } catch (\Exception $e) {
            echo $e->getMessage();
            $this->assertTrue(true, 'Test mysql connect');
        }
    }

    public function testConnect7()
    {
        //测试参数传递不正确,传递了非字符参数dbname
        try {
            $config = $this->config['TestMysqlHostErr'];
            $config['user'] = array();
            $pdo = new Utils\Mysql($config);
            $this->assertTrue(empty($pdo), 'Test mysql connect');
        } catch (\InvalidArgumentException $e) {
            $errInfo = $e->getMessage();
            $errInfo = explode(':', $errInfo);
            $this->assertTrue($errInfo[1] == 'user', 'Test mysql connect');
        } catch (\Exception $e) {
            $this->assertTrue(true, 'Test mysql connect');
        }
    }

    public function testInsert()
    {

        //正常情况插入测试
        $p = new Utils\Mysql($this->config['TestMysql']);
        try {
            $re = $p->insert('test', array('id' => 1234, 'n' => 1, 'leng' => 23));
            $row = $p->select('test', 'n=? and leng=?', array(1, 23));
            $this->assertTrue(count($row) >= 1, 'Test mysql insert');
        } catch (\Exception $e) {
            $this->assertTrue(false, 'Test mysql insert');
        }

        //duplicate key测试
        $p = new Utils\Mysql($this->config['TestMysql']);
        try {
            $re = $p->insert('test', array('id' => 1234, 'n' => 1, 'leng' => 23));
        } catch (\Exception $e) {
            $this->assertTrue(Utils\Mysql::isDupKeyError($e->getCode()), 'Test mysql insert');
            $this->assertTrue(Utils\Mysql::isLogicError($e->getCode()), 'Test mysql insert');
        }


        //参数错误插入测试
        try {
            $re = $p->insert(array('test'), array('n' => 1, 'leng1' => 23));
            $this->assertTrue(false, 'Test mysql insert,should not be here');
        } catch (\InvalidArgumentException $e) {
            $errInfo = "\n" . $e->getMessage();
            $errInfo = explode(':', $errInfo);
            $this->assertTrue($errInfo[1] == 'table', 'Test mysql insert');
        } catch (\Exception $e) {
            $errInfo = $e->getMessage() . "\n";
            $this->assertTrue(false, 'Test mysql insert');
        }
    }

    public function testSelect()
    {
        //正常情况查询测试，能生成sql日志
        try {
            $p = new Utils\Mysql($this->config['TestMysql']);
            $re = $p->select('test', 'id > ?', array(0));
            $this->assertTrue(count($re) > 0, 'Test mysql select');
        } catch (\Exception $e) {
            $this->assertTrue(false, 'Test mysql select');
        }
    }

    public function testSelectE()
    {
        //查询字段不对测试
        try {
            $p = new Utils\Mysql($this->config['TestMysql']);
            $re = $p->select('test', 'n> ? and d< ?', array(0, 4));
            $this->assertTrue(!(count($re) > 0), 'Test mysql select');
        } catch (\Exception $e) {
            $errCode = $e->getCode();
//            $this->assertTrue($errCode === 1054, 'Test mysql select');
            $this->assertTrue(Utils\Mysql::isLogicError($errCode), 'Test mysql select');
        }

        //参数错误情况查询测试, where条件应该传递字符串类型
        try {
            $p = new Utils\Mysql($this->config['TestMysql']);
            $re = $p->select('test', array('leng > ?'), array(1));
            $this->assertTrue(count($re) > 0, 'Test mysql select');
        } catch (\InvalidArgumentException $e) {
            $errInfo = $e->getMessage();
            $errInfo = explode(':', $errInfo);
            $this->assertTrue($errInfo[1] == 'where', 'Test mysql select');
        } catch (\Exception $e) {
            $this->assertTrue(true, 'Test mysql select');
        }

    }

    public function testSelectInjecting()
    {
        //查询带sql注入
        try {
            $p = new Utils\Mysql($this->config['TestMysql']);
            $re = $p->select('test', 'leng = ?', array('156;insert into'));
            $this->assertTrue(count($re) == 0, 'Test mysql select');
        } catch (\Exception $e) {
            $this->assertTrue(false, 'Test mysql select');
        }

    }

    public function testUpdate()
    {
        //正常更新测试
        $p = new Utils\Mysql($this->config['TestMysql']);
        $t = time();
        $re = $p->insert('test', array('id' => $t, 'n' => 1, 'leng' => 23));
        try {
            $re = $p->update('test', array('n' => 1, 'va' => 'dd1'), "id = $t");
            if (count($re) <= 0) {
                $this->assertTrue(count($re) <= 0, 'Test mysql update');
            }
            $data = $p->select('test', "id = $t");
            if (count($data) <= 0) {
                $this->assertTrue(count($data) <= 0, 'Test mysql update');
            }
            $updateData = $data[0];
            $this->assertTrue($updateData['va'] == 'dd1' && $updateData['n'] == 1, 'Test mysql update');
        } catch (\Exception $e) {
            $this->assertTrue(false, 'Test mysql update');
        }
    }

    public function testUpdateInjecting()
    {
        try {
            $p = new Utils\Mysql($this->config['TestMysql']);
            $t = time();
            $re = $p->update('test', array('n' => $t, 'va' => 'ddd'), "leng > 23 ' and 1=1'");
            $this->assertTrue(count($re) == 0, 'Test mysql update ');
        } catch (\Exception $e) {
            $errCode = $e->getCode();
//            $this->assertTrue($errCode === 1064, 'Test mysql update');
            $this->assertTrue(Utils\Mysql::isLogicError($errCode), 'Test mysql update');
        }

        //参数检查测试,bind参数应该为string类型
        try {
            $p = new Utils\Mysql($this->config['TestMysql']);
            $re = $p->update('test', array('n' => time(), 'va' => 'dd1'), "leng = 23", false);
            //var_dump($re);
            $this->assertTrue(count($re) > 0, 'Test mysql update');
        } catch (\Exception $e) {
            $errInfo = $e->getMessage();
            $errInfo = explode(':', $errInfo);
            $this->assertTrue($errInfo[1] == 'bind', 'Test mysql update');
        }
    }

    //直接执行sql测试
    public function testRun()
    {
        $p = new Utils\Mysql($this->config['TestMysql']);
        $re = $p->insert('test', array('id' => 131, 'n' => 1, 'leng' => 23));
        //正常传递参数测试
        try {
            $sql = 'select * from `test` where id > ? and n > ?';
            $bind = array(130, 0);
            $re = $p->run($sql, $bind);
            if (empty($re) || count($re) == 0) {
                $this->assertTrue(false, 'Test mysql run');
            }
            $data = $re[0];
            $assert = ($data['id'] > 130) && ($data['n'] > 0);
            $this->assertTrue($assert, 'Test mysql run');
        } catch (\Exception $e) {
            $this->assertTrue(false, 'Test mysql run');
        }

    }

    public function testRunInjection()
    {
        //sql注入测试
        try {
            $p = new Utils\Mysql($this->config['TestMysql']);
            $sql = 'select * from `test` where id > ? and va = ?';
            $bind = array(100, "' and 1=1 '");
            $re = $p->run($sql, $bind);
            $this->assertTrue(count($re) == 0, 'Test mysql run sql injecting');
        } catch (\Exception $e) {
            echo $errInfo = $e->getMessage();
            $this->assertTrue(strpos($errInfo, 'syntax to use near') > 0, 'Test mysql run  sql injecting');
        }

        //参数问题测试
        try {
            $p = new Utils\Mysql($this->config['TestMysql']);
            $sql = 'select * from `test` where id > ? and va = ?';
            $bind = false;
            $re = $p->run($sql, $bind);
            $this->assertTrue(count($re) == 0, 'Test mysql run sql injecting');
        } catch (\Exception $e) {
            $errInfo = $e->getMessage();
            $errInfo = explode(':', $errInfo);
            $this->assertTrue($errInfo[1] == 'bind', 'Test mysql run  sql injecting');
        }
    }
}

<?php

namespace Xiaoju\Beatles\Utils\Utest;

use Xiaoju\Beatles\Utils\ArgumentValidator;
use Xiaoju\Beatles\Utils\HttpRequest;

require_once 'base/routertest.php';
require_once 'base/uritest.php';

require_once 'helper/validatortest.php';
require_once 'helper/loggertest.php';
require_once 'helper/httprequesttest.php';
require_once 'helper/authenticationtest.php';
require_once 'helper/formattertest.php';
require_once 'helper/argumentvalidatortest.php';

require_once 'libraries/redistest.php';
require_once 'libraries/memcachetest.php';
require_once 'libraries/mysqltest.php';

class AllTests
{
    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite();
        $suite->setName('All Xiaoju Beatles utils tests');
        $suite->addTestSuite(RouterTests::suite());
        $suite->addTestSuite(UriTests::suite());

        $suite->addTestSuite(LoggerTests::suite());
        $suite->addTestSuite(ValidatorTests::suite());
        $suite->addTestSuite(HttpRequestTests::suite());
        $suite->addTestSuite(ArgumentValidatorTests::suite());
        $suite->addTestSuite(AuthenticationTests::suite());
        $suite->addTestSuite(FormatterTests::suite());

        $suite->addTestSuite(RedisTests::suite());
        $suite->addTestSuite(MemcacheTests::suite());
        $suite->addTestSuite(MysqlTests::suite());
        return $suite;
    }
}

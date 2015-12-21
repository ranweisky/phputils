<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/1/8
 * Time: 0:09
 */
function logFinish($in)
{
    //这里由于没有做ob_start,如果输出的数据太大超出缓冲区被自动flush出去的话, ob_get_contents拿到的数据就是空的
    //日志里的out就是空
    global $__uid;
    $in['__uid'] = $__uid;
    \Xiaoju\Beatles\Utils\Logger::notice(
        '',
        0,
        array('in' => json_encode($in), 'out' => ob_get_contents())
    );
}

date_default_timezone_set('Asia/Shanghai');
$loader->addPsr4('Xiaoju\Beatles\Framework\\', FRAMEPATH . '');
$loader->addPsr4('Xiaoju\Beatles\Utils\\', FRAMEPATH . 'helper');
$loader->addPsr4('Xiaoju\Beatles\Utils\\', FRAMEPATH . 'libraries');
$loader->addPsr4('Xiaoju\Beatles\Utils\\', FRAMEPATH . 'config');

$errNo = 0;
$errMsg = '';
$__uid = 0;
try {
    //生成全局的logid
    \Xiaoju\Beatles\Utils\Logger::create(
        $logConfig['intLevel'],
        $logConfig['strLogFile'],
        $logConfig['intMaxFileSize']
    );
    \Xiaoju\Beatles\Utils\Logger::setLogId(Xiaoju\Beatles\Utils\Logger::getLogId());
    $params = array('get' => $_GET, 'post' => $_POST);
    register_shutdown_function('logFinish', $params);
    $routerConfig = $appNameSpace . '\Config\Route';
    if (class_exists($routerConfig) && is_array($routerConfig::$routes)) {
        $routerConfig = $routerConfig::$routes;
    } else {
        $routerConfig = array();
    }
    $router = new \Xiaoju\Beatles\Framework\Base\Router($_SERVER['REQUEST_URI'], $routerConfig);
    $router->setRoute();
    $router->run($params);
} catch (\InvalidArgumentException $ex) {
    $errNo = -1;
    $errMsg = strlen($ex->getMessage()) ? $ex->getMessage() : 'system error';
    \Xiaoju\Beatles\Utils\Logger::fatal(
        $errMsg,
        $errNo,
        array('in' => json_encode($params), 'out' => ob_get_contents())
    );
    echo \Xiaoju\Beatles\Utils\Formatter::format(array('errno' => $errNo, 'errmsg' => $errMsg));
} catch (\Exception $ex) {
    $errNo = $ex->getCode();
    $errMsg = $ex->getMessage();
    \Xiaoju\Beatles\Utils\Logger::warning(
        $errMsg,
        $errNo,
        array('in' => json_encode($params), 'out' => ob_get_contents())
    );
    echo \Xiaoju\Beatles\Utils\Formatter::format(array('errno' => $errNo, 'errmsg' => $errMsg));
}

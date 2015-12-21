<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2014/12/30
 * Time: 21:37
 */

//这里需要根据实际需求修改
define('FRAMEPATH', '/home/webroot/webroot/phputils/src/');
define('APPPATH', '/home/webroot/app/babypig/');

global $logConfig;
global $appNameSpace;

$logConfig = array(
    'intLevel' => 0xff,
    'strLogFile' => APPPATH . '/log/didi.log',
    'intMaxFileSize' => 0, //0为不限制
);
$appNameSpace = 'Xiaoju\Beatles\App\Babypig';

//注册autoloader
require_once(FRAMEPATH . '/autoload/autoloader.php');
$loader = Xiaoju\Beatles\Framework\Autoload\Autoloader::getLoader();
$loader->addPsr4('Xiaoju\Beatles\App\Babypig\\', APPPATH);
//end 这里需要根据实际需求修改

require_once(FRAMEPATH . '/framework.php');

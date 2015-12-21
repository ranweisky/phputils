<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2014/12/30
 * Time: 11:22
 */
namespace Xiaoju\Beatles\Framework\Base;

/**
 * Class Router
 * @package Xiaoju\Beatles\Framework\Base
 * URIPATH为 /operator/object/operation
 * 会被路由到 /controller/operator/object/operation.php的class operation的index方法
 */
class Router
{
    const DEFAULT_MODULE = 'index';
    const DEFAULT_CONTROLLER = 'index';
    const DEFAULT_METHOD = 'index';
    const FUNCTIONNAME = 'index';

    private $moduleName;
    private $controllerName;
    private $methodName;
    private $controller = null;

    private $uri;
    private $route;

    public function __construct($uri, array $route = array())
    {
        $this->uri = $uri;
        $this->route = $route;
    }

    private function getSegments()
    {
        $uri = $this->uri;
        foreach ($this->route as $key => $val) {
            if (preg_match('#^' . $key . '$#', $uri)) {
                if (strpos($val, '$') !== false && strpos($key, '(') !== false) {
                    $uri = preg_replace('#^' . $key . '$#', $val, $this->uri);
                }
            }
        }
        $uri = new Uri($uri);
        $segments = $uri->explodeSegments();
        return $segments;
    }

    /**
     *
     */
    public function setRoute()
    {
        $segments = $this->getSegments();
        $this->moduleName = isset($segments[0]) ? $segments[0] : self::DEFAULT_MODULE;
        $this->controllerName = isset($segments[1]) ? $segments[1] : self::DEFAULT_CONTROLLER;
        $this->methodName = isset($segments[2]) ? $segments[2] : self::DEFAULT_METHOD;

        $className = sprintf(
            '%s\\Controller\\%s\\%s\\%s',
            $GLOBALS['appNameSpace'],
            ucwords($this->moduleName),
            ucwords($this->controllerName),
            ucwords($this->methodName)
        );
        if (class_exists($className)) {
            if (method_exists($className, self::FUNCTIONNAME)) {
                $method = new \ReflectionMethod($className, self::FUNCTIONNAME);
                if ($method->isPublic() && $method->getName() === self::FUNCTIONNAME) {
                    $this->controller = new $className;
                    return true;
                }
            }
        }
        //退到/module/index.php
        $this->methodName = self::DEFAULT_METHOD;
        $this->controllerName = self::DEFAULT_CONTROLLER;
        $className = sprintf(
            '%s\\Controller\\%s\\%s',
            $GLOBALS['appNameSpace'],
            ucwords($this->moduleName),
            ucwords($this->controllerName)
        );
        if (class_exists($className) && method_exists($className, self::FUNCTIONNAME)) {
            $this->controller = new $className;
            return true;
        }

        $this->moduleName = self::DEFAULT_MODULE;
        $className = sprintf('%s\\Controller\\%s', $GLOBALS['appNameSpace'], ucwords($this->moduleName));
        if (class_exists($className) && method_exists($className, self::FUNCTIONNAME)) {
            $this->controller = new $className;
            return true;
        }

        if (is_null($this->controller)) {
            throw new \BadMethodCallException('wrong route path', -1);
        }
    }

    public function run(array $params)
    {
        if (is_null($this->controller)) {
            throw new \BadMethodCallException('wrong router', -1);
        }
        return call_user_func_array(array($this->controller, self::FUNCTIONNAME), array($params));
    }
}

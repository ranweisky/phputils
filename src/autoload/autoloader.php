<?php
namespace Xiaoju\Beatles\Framework\Autoload;

class Autoloader
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Xiaoju\Beatles\Framework\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        spl_autoload_register(array('\Xiaoju\Beatles\Framework\Autoload\Autoloader', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Xiaoju\Beatles\Framework\Autoload\ClassLoader();
        spl_autoload_unregister(array('\Xiaoju\Beatles\Framework\Autoload\Autoloader', 'loadClassLoader'));

        $loader->addPsr4('', __DIR__);
        /*
        $map = require __DIR__ . '/autoload_psr4.php';
        foreach ($map as $namespace => $path) {
            $loader->setPsr4($namespace, $path);
        }*/
        $loader->register(true);
        return $loader;
    }
}

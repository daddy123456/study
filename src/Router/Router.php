<?php


namespace Src\Router;

/**
 * @method static void addRoute($httpMethod, string $route, $handler, array $options = [])
 * @method static void get(string $route, $handler, array $options = [])
 * @method static void post(string $route, $handler, array $options = [])
 */
class Router
{
    protected static $serverName = 'http';

    /**
     * @var DispatcherFactory
     */
    protected static $factory;

    public static function __callStatic($name, $arguments)
    {
        $router = static::$factory->getRouter(static::$serverName);
        return $router->{$name}(...$arguments);
    }

    public static function init(DispatcherFactory $factory)
    {
        static::$factory = $factory;
    }
}

<?php


namespace Src\Router;



use FastRoute\DataGenerator\GroupCountBased;
use FastRoute\Dispatcher;
use FastRoute\RouteParser\Std;

class DispatcherFactory
{
    protected $routes = [BASE_PATH . '/config/router.php'];

    /**
     */
    protected $routers = [];

    /**
     * @var Dispatcher[]
     */
    protected $dispatchers = [];

    public function __construct()
    {
        $this->initConfigRoute();
    }

    public function initConfigRoute()
    {
        Router::init($this);
        foreach ($this->routes as $route) {
            if (file_exists($route)) {
                require_once $route;
            }
        }
    }

    public function getDispatcher(string $serverName): Dispatcher
    {
        if (isset($this->dispatchers[$serverName])) {
            return $this->dispatchers[$serverName];
        }

        $router = $this->getRouter($serverName);
        return $this->dispatchers[$serverName] = new Dispatcher\GroupCountBased($router->getData());
    }

    public function getRouter(string $serverName): RouteCollector
    {
        if (isset($this->routers[$serverName])) {
            return $this->routers[$serverName];
        }

        $parser = new Std();
        $generator = new GroupCountBased();
        return $this->routers[$serverName] = new RouteCollector($parser, $generator, $serverName);
    }
}

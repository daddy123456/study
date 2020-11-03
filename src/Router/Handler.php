<?php


namespace Src\Router;


class Handler
{
    /**
     * @var array|callable|string
     */
    public $callback;

    /**
     * @var string
     */
    public $route;

    /**
     * @var array
     */
    public $options;

    public function __construct($callback, string $route, array $options = [])
    {
        $this->callback = $callback;
        $this->route = $route;
        $this->options = $options;
    }
}

<?php


namespace src\Router;


use FastRoute\DataGenerator;
use FastRoute\RouteParser;

class RouteCollector
{
    /**
     * @var string
     */
    protected $server;

    /**
     * @var RouteParser
     */
    protected $routeParser;

    /**
     * @var DataGenerator
     */
    protected $dataGenerator;

    /**
     * @var string
     */
    protected $currentGroupPrefix;

    /**
     * @var array
     */
    protected $currentGroupOptions = [];

    /**
     * Constructs a route collector.
     */
    public function __construct(RouteParser $routeParser, DataGenerator $dataGenerator, string $server = 'http')
    {
        $this->routeParser = $routeParser;
        $this->dataGenerator = $dataGenerator;
        $this->currentGroupPrefix = '';
        $this->server = $server;
    }

    public function addRoute($httpMethod, string $route, $handler, array $options = [])
    {
        $route = $this->currentGroupPrefix . $route;
        $routeDatas = $this->routeParser->parse($route);
        $options = $this->mergeOptions($this->currentGroupOptions, $options);
        foreach ((array) $httpMethod as $method) {
            $method = strtoupper($method);
            foreach ($routeDatas as $routeData) {
                $this->dataGenerator->addRoute($method, $routeData, new Handler($handler, $route, $options));
            }
        }
    }
    protected function mergeOptions(array $origin, array $options): array
    {
        return array_merge_recursive($origin, $options);
    }

    public function getData(): array
    {
        return $this->dataGenerator->getData();
    }
}

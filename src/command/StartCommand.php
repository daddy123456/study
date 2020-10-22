<?php

declare(strict_types=1);

namespace src\command;


use Swoole\Http\Server;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StartCommand extends Command
{
    protected function configure()
    {
        $this->setName('start')->setDescription('server run..');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $http = new Server('0.0.0.0', 9501);

        $http->on('request', function ($request, $response) {
            if ($request->server['path_info'] == '/favicon.ico' || $request->server['request_uri'] == '/favicon.ico') {
                $response->end();
                return;
            }

            $dispatcher = \FastRoute\simpleDispatcher(function(\FastRoute\RouteCollector $r) {
                $r->addRoute('GET', '/index', 'App\Controller\IndexController@index');
            });

            // Fetch method and URI from somewhere
            $httpMethod = $request->server['request_method'];
            $uri = $request->server['request_uri'];

            // Strip query string (?foo=bar) and decode URI
            if (false !== $pos = strpos($uri, '?')) {
                $uri = substr($uri, 0, $pos);
            }
            $uri = rawurldecode($uri);
            $routeInfo = $dispatcher->dispatch($httpMethod, $uri);
            switch ($routeInfo[0]) {
                case \FastRoute\Dispatcher::NOT_FOUND:
                    $response->status(404);
                    $response->end();
                    break;
                case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                    $allowedMethods = $routeInfo[1];
                    $response->status(405);
                    $response->end();
                    break;
                case \FastRoute\Dispatcher::FOUND:
                    $handler = $routeInfo[1];
                    $vars = $routeInfo[2];

                    [$class, $action] = explode('@', $handler);
                    (new $class())->{$action}(11);
                    $response->header("Content-Type", "text/html; charset=utf-8");
                    $response->end();
                    break;
            }
        });

        $http->start();
    }
}

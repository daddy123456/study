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
            $dispatcher = \FastRoute\simpleDispatcher(function(\FastRoute\RouteCollector $r) {
                $r->addRoute('GET', '/index', 'App\Controller\IndexController@index');
                // {id} must be a number (\d+)
                $r->addRoute('GET', '/user/{id:\d+}', 'get_user_handler');
                // The /{title} suffix is optional
                $r->addRoute('GET', '/articles/{id:\d+}[/{title}]', 'get_article_handler');
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
                    // ... 404 Not Found
                    $response->header("Content-Type", "text/html; charset=utf-8");
                    $response->end('Not Found');
                    break;
                case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                    $allowedMethods = $routeInfo[1];
                    // ... 405 Method Not Allowed
                    $response->header("Content-Type", "text/html; charset=utf-8");
                    $response->end('Method Not Allowed');
                    break;
                case \FastRoute\Dispatcher::FOUND:
                    $handler = $routeInfo[1];
                    $vars = $routeInfo[2];

                    // ... call $handler with $vars
                    [$class, $action] = explode('@', $handler);
                    (new $class())->{$action}(11);
                    $response->header("Content-Type", "text/html; charset=utf-8");
                    $response->end('ok');
                    break;
            }
        });

        $http->start();
    }
}

<?php

declare(strict_types=1);

namespace src\command;


use FastRoute\Dispatcher;
use src\Router\DispatcherFactory;
use Swoole\Http\Server;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StartCommand extends Command
{
    /** @var $dispatcher */
    protected $dispatcher;

    /** @var string  */
    protected $serverName = 'http';

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->dispatcher = (new DispatcherFactory())->getDispatcher($this->serverName);
        parent::initialize($input, $output);
    }

    protected function configure()
    {
        $this->setName('start')->setDescription('server run..');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $http = new Server('0.0.0.0', 9501, 1, 1);
        $http->on('request', function ($request, $response) {
            // Fetch method and URI from somewhere
            $httpMethod = $request->server['request_method'];
            $uri = $request->server['request_uri'];

            // Strip query string (?foo=bar) and decode URI
            if (false !== $pos = strpos($uri, '?')) {
                $uri = substr($uri, 0, $pos);
            }
            $uri = rawurldecode($uri);

            $routeInfo = $this->dispatcher->dispatch($httpMethod, $uri);
            switch ($routeInfo[0]) {
                case Dispatcher::NOT_FOUND:
                    $response->status(404);
                    $response->end('NOT_FOUND');
                    break;
                case Dispatcher::METHOD_NOT_ALLOWED:
                    $allowedMethods = $routeInfo[1];
                    $response->status(405);
                    $response->end("METHOD_NOT_ALLOWED: $allowedMethods[0]");
                    break;
                case Dispatcher::FOUND:
                    $handler = $routeInfo[1];
                    $vars = $routeInfo[2];
                    $callback = $handler->callback;
                    if ($callback instanceof \Closure) {
                        $callback(...$vars);
                    } else {
                        [$className, $action] = explode('@', $callback);
                        call_user_func_array(array(new $className, $action), $vars);
                    }
                    $response->header("Content-Type", "text/html; charset=utf-8");
                    $response->end();
                    break;
            }
        });

        $http->start();
    }
}

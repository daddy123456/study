<?php

declare(strict_types=1);

namespace Src\Command;


use FastRoute\Dispatcher;
use Src\Request\Context;
use Src\Router\DispatcherFactory;
use Swoole\Http\Request as SwooleRequest;
use Swoole\Http\Server;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StartCommand extends Command
{
    /** @var $dispatch */
    protected $dispatch;

    /** @var string  */
    protected $serverName = 'http';

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('server start ...');
        $this->dispatch = (new DispatcherFactory())->getDispatcher($this->serverName);
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
            Context::setContext(SwooleRequest::class, $request);
            $routeInfo = $this->dispatch->dispatch(
                $request->server['request_method'],
                rawurldecode($request->server['request_uri'])
            );
            $this->dispatcher($routeInfo, $response);
        });

        $http->start();
    }

    private function dispatcher(array $routeInfo, $response)
    {
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
    }
}

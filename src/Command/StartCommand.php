<?php

declare(strict_types=1);

namespace Src\Command;


use FastRoute\Dispatcher;
use Src\Request\Context;
use Src\Router\DispatcherFactory;
use Swoole\Http\Request as SwooleRequest;
use Swoole\Http\Response as SwooleResponse;
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

    protected function configure()
    {
        $this->setName('start')->setDescription('start server');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('start server');
        $http = new Server('0.0.0.0', 9501, 1, 1);
        $http->on('workerStart', [$this, 'onWorkerStart']);
        $http->on('request', [$this, 'onRequest']);
        $http->start();
    }

    public function onWorkerStart(Server $server)
    {
        file_put_contents(BASE_PATH . '/master.pid', $server->master_pid);
        $this->dispatch = (new DispatcherFactory())->getDispatcher($this->serverName);
    }

    public function onRequest(SwooleRequest $request, SwooleResponse $response)
    {
        Context::setContext(SwooleRequest::class, $request);
        $routeInfo = $this->dispatch->dispatch(
            $request->server['request_method'],
            rawurldecode($request->server['request_uri'])
        );
        $this->dispatcher($routeInfo, $response);
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
                $result = $this->callback($handler->callback, $vars);
                $response->header("Content-Type", "text/html; charset=utf-8");
                $response->end($result);
                break;
        }
    }

    private function callback($callable, $vars)
    {
        if ($callable instanceof \Closure) {
            $result = $callable(...$vars);
        } else {
            [$className, $action] = explode('@', $callable);
            $result = call_user_func_array(array(new $className, $action), $vars);
        }
        return $result;
    }
}

<?php

declare(strict_types=1);

namespace Src\HttpServer;


use FastRoute\Dispatcher;
use Src\Request\Context;
use Src\Router\DispatcherFactory;
use Swoole\Http\Request as SwooleRequest;
use Swoole\Http\Response as SwooleResponse;
use Swoole\Http\Server as SwooleHttpServer;

class Server
{
    /** @var $dispatch */
    protected $dispatch;

    /** @var string  */
    protected $serverName = 'http';

    /** @var SwooleHttpServer */
    protected $server;

    public function __construct()
    {
        /** @var HttpServerConfig */
        $serverConfig = new HttpServerConfig();

        /** @var SwooleHttpServer */
        $this->server = new SwooleHttpServer(
            $serverConfig->host(),
            $serverConfig->port(),
            $serverConfig->mode(),
            $serverConfig->sockType()
        );

        /** register callback events */
        $this->server->on('request', [$this, 'onRequest']);
        $this->server->on('workerStart', [$this, 'onWorkerStart']);
    }

    public function run()
    {
        $this->server->start();
    }

    public function onWorkerStart(SwooleHttpServer $server)
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

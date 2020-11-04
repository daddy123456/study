<?php

declare(strict_types=1);

namespace Src\Request;


use Src\Contract\RequestInterface;
use Swoole\Http\Request as SwooleRequest;


class Request implements RequestInterface
{
    /** @var SwooleRequest */
    private $request;

    public function __construct()
    {
        $this->request = Context::getContext(SwooleRequest::class);
    }

    public function get()
    {
        return $this->request->get;
    }

    public function getMethod()
    {
        return $this->request->server['request_method'];
    }

    public function getUri()
    {
        return $this->request->server['request_uri'];
    }

    public function getHeaders()
    {
        return $this->request->header;
    }

    public function getBody()
    {
        return $this->request->rawContent();
    }

    public function post()
    {
        return $this->request->post;
    }

    /**
     * @return SwooleRequest
     */
    public function getRequest(): SwooleRequest
    {
        return $this->request;
    }
}

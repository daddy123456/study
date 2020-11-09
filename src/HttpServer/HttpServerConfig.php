<?php

declare(strict_types=1);

namespace Src\HttpServer;


class HttpServerConfig
{
    /** @var array */
    protected $serverConfig;

    public function __construct()
    {
        $this->serverConfig = require BASE_PATH . '/config/config.php';
    }

    public function host()
    {
        return $this->serverConfig['server']['host'];
    }

    public function port()
    {
        return $this->serverConfig['server']['port'];
    }

    public function mode()
    {
        return $this->serverConfig['mode'];
    }

    public function sockType()
    {
        return $this->serverConfig['server']['sock_type'];
    }
}

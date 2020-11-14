<?php

declare(strict_types=1);

namespace Src\Redis;


use Src\Contract\ConfigAutoload;
use Src\Request\ApplicationContext;

class Redis
{
    /** @var string */
    protected $name = 'default';

    /** @var array */
    protected $config;

    public function adapter($name): \Redis
    {
        if (is_array($name)) {
            return $this->conn($name);
        }

        $redisConfigs = ApplicationContext::getContainer()->get(ConfigAutoload::class)->get('redis');
        if (isset($redisConfigs[$name])) {
            return $this->conn($redisConfigs[$name]);
        }
        return $this->conn($redisConfigs['default']);
    }

    private function conn(array $config)
    {
        $host = $config['host'] ?? 'localhost';
        $port = $config['port'] ?? 6379;
        $timeout = $config['timeout'] ?? 3;
        $db = $config['db'] ?? 0;
        $redis = new \Redis();
        $redis->connect($host, $port, $timeout);
        if ($db) {
            $redis->select($db);
        }
        return $redis;
    }
}

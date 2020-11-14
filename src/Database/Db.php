<?php

declare(strict_types=1);

namespace Src\Database;


use Src\Contract\ConfigAutoload;
use Src\Redis\Redis;
use Src\Request\ApplicationContext;

class Db
{
    /** @var string */
    protected static $queryBuider;

    /** @var array */
    protected $config;

    /** @var string 默认配置 */
    protected $adapter = 'default';

    /** @var \mysqli $mysql */
    protected $mysql;

    /** @var Redis $redis */
    protected $redis;

    /** @var self */
    protected static $self;

    public function __construct()
    {
        $this->redis = ApplicationContext::getContainer()->get(Redis::class)->adapter('default');
        $this->config = ApplicationContext::getContainer()->get(ConfigAutoload::class)->get('databases')[$this->adapter];
    }

    private function conn()
    {
        $mysqli = new \mysqli();
        $host = $this->config['host'];
        $port = $this->config['port'];
        $user = $this->config['username'];
        $pwd = $this->config['password'];
        $dbName = $this->config['database'];
        $charset = $this->config['charset'];
        $mysqli->connect($host, $user, $pwd, $dbName, $port);
        $mysqli->set_charset($charset);
        $this->mysql = $mysqli;
    }

    public static function table(string $name): self
    {
        self::$queryBuider = 'SELECT * FROM ' . $name;
        self::$self = new self();
        return self::$self;
    }

    public  function where(string $filed, string $operator, $value)
    {
        self::$queryBuider .= ' WHERE ' . $filed . $operator . $value;
        return self::$self;
    }

    public  function query()
    {
        $key = md5(self::$queryBuider);
        if (! $this->redis->exists($key)) {
            $this->conn();
            $result =  $this->mysql->query(self::$queryBuider);
            if (! $result) {
                throw new \Exception($this->mysql->error);
            }

            /** @var array $items */
            $items = [];
            for ($i = 1; $i <= $result->num_rows; $i++) {
                $items[] = $result->fetch_assoc();
            }

            /** redis cache */
            $this->redis->set($key, serialize($items));
            $this->redis->expire($key, 60);

            /** close */
            $result->close();
            $this->mysql->close();
        }

        return unserialize($this->redis->get($key));
    }

    public function save(array $data)
    {

    }
}

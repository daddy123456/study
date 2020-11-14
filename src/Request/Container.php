<?php

declare(strict_types=1);

namespace Src\Request;


use Src\Command\StartCommand;
use Src\Config\ConfigFactory;
use Src\Contract\ApplicationInterface;
use Src\Contract\ConfigAutoload;
use Src\Contract\ContainerInterface;
use Psr\Container\ContainerInterface as PsrContainerInterface;
use Symfony\Component\Console\Application;

class Container implements ContainerInterface
{
    /** @var array  */
    private $entrys = [];

    public function __construct()
    {
        $this->entrys = [
            self::class => $this,
            PsrContainerInterface::class => $this,
            ContainerInterface::class => $this,
            ApplicationInterface::class => Application::class,
            ConfigAutoload::class => (new ConfigFactory())()
        ];
    }

    public function set(string $name, $entry)
    {
        $this->entrys[$name] = $entry;
    }

    public function get($id)
    {
        if (isset($this->entrys[$id]) || array_key_exists($id, $this->entrys)) {
            $container = $this->entrys[$id];
            if (Application::class == $container) {
                $container =  new $container;
                $container->add(new StartCommand());
            }
            return $container;
        }
        if (! class_exists($id)) {
            throw new \Exception('Class does not exist');
        }
        $class = new $id;
        if (! $class instanceof Request) {
            $this->entrys[$id] = $class;
        }
        return $class;
    }

    public function has($id)
    {
        return isset($this->entrys[$id]);
    }
}

<?php

declare(strict_types=1);

namespace Src\Request;


use Psr\Container\ContainerInterface;

class ApplicationContext
{
    /**
     * @var null|ContainerInterface
     */
    private static $container;

    /**
     * @return ContainerInterface|null
     */
    public static function getContainer(): ContainerInterface
    {
        return self::$container;
    }

    /**
     * @param ContainerInterface|null $container
     */
    public static function setContainer(ContainerInterface $container): ContainerInterface
    {
        self::$container = $container;
        return $container;
    }
}
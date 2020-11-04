<?php

declare(strict_types=1);

use Src\Request\Container;
use Src\Request\ApplicationContext;

$container = new Container();

if (! $container instanceof \Psr\Container\ContainerInterface) {
    throw new RuntimeException('container is invalid.');
}
return ApplicationContext::setContainer($container);

<?php


namespace Src\Contract;

use Psr\Container\ContainerInterface as PsrContainerInterface;

interface ContainerInterface extends PsrContainerInterface
{
    /** @param mixed $entry */
    public function set(string $name, $entry);
}
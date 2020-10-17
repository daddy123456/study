<?php

declare(strict_types=1);
namespace src\contract;


interface ConfigInterface
{
    public function get(string $key, $default = null);


    public function has(string $keys);


    public function set(string $key, $value);
}

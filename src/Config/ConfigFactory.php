<?php

declare(strict_types=1);

namespace Src\Config;


use Symfony\Component\Finder\Finder;

class ConfigFactory
{
    public function __invoke()
    {
        return new Config($this->readAutoloadConfig(BASE_PATH . '/config/autoload'));
    }

    private function readAutoloadConfig(string $paths): array
    {
        $configs = [];
        $finder = new Finder();
        $finder->files()->in($paths)->name('*.php');
        foreach ($finder as $file) {
            $configs[$file->getBasename('.php')] = require $file->getRealPath();
        }
        return $configs;
    }
}

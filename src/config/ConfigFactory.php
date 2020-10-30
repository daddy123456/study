<?php

declare(strict_types=1);

namespace src\config;


use Symfony\Component\Finder\Finder;

class ConfigFactory
{
    public function __invoke()
    {
        $basePath = BASE_PATH . '/config/';
        $config = $this->readConfig($basePath. 'config.php');
        $autoloadConfig = $this->readAutoloadConfig(BASE_PATH . '/config/autoload');
        $merged =  array_merge_recursive($config, $autoloadConfig);
        return new Config($merged);
    }

    private function readConfig(string $configPath): array
    {
        $config = [];
        if (file_exists($configPath) && is_readable($configPath)) {
            $config = require $configPath;
        }
        return is_array($config) ? $config : [];
    }

    private function readAutoloadConfig(string $paths): array
    {
        $configs = [];
        $finder = new Finder();
        $finder->files()->in($paths)->name('*.php');
        foreach ($finder as $file) {
            $configs[] = [$file->getBasename('.php') => require $file->getRealPath()];
        }
        return $configs;
    }
}

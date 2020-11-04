<?php

declare(strict_types = 1);

namespace Src\Request;


class Context
{
    private static $context;

    /**
     * @param mixed $context
     */
    public static function setContext(string $name, $context): void
    {
        self::$context[$name] = $context;
    }

    /**
     * @return mixed
     */
    public static function getContext(string $name)
    {
        if (! isset(self::$context[$name])) {
            return null;
        }
        return self::$context[$name];
    }
}

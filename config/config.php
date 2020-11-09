<?php

declare(strict_types=1);

return [
    'mode' => SWOOLE_PROCESS,
    'server' => [
        'type' => 1,
        'host' => '0.0.0.0',
        'port' => 9501,
        'sock_type' => SWOOLE_SOCK_TCP,
    ],
];

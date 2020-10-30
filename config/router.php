<?php

declare(strict_types=1);

use src\Router\Router;

Router::addRoute('get','/index', '\\App\\Controller\\IndexController@index');

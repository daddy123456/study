<?php

declare(strict_types=1);

use Src\Router\Router;

Router::addRoute('get','/index', '\\App\\Controller\\IndexController@index');
Router::addRoute('get','/test', '\\App\\Controller\\IndexController@test');

Router::get('/' , function () {
    return 'Hello';
});

Router::get('/favicon.ico' , function () {
    return '';
});

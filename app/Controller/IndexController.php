<?php

declare(strict_types=1);

namespace App\Controller;


use App\AbstractController;
use Src\Redis\Redis;
use Src\Request\ApplicationContext;

class IndexController extends AbstractController
{
    public function index()
    {
        $redis = ApplicationContext::getContainer()->get(Redis::class)->adapter('default');

        var_dump($redis->keys('*'));


        if (! $redis->exists('php')) {
            $redis->set('php', 'Hello');
        }
        var_dump($redis->get('php'));
    }

    public function test()
    {
        var_dump($this->request->getUri());
        var_dump($this->request->get());
        return true;
    }
}
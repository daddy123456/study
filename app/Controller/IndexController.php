<?php

declare(strict_types=1);

namespace App\Controller;


use App\AbstractController;
use Src\Database\Db;
use Src\Model\Model;
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
//        var_dump($this->request->getUri());
//        var_dump($this->request->get());
//        $model = new Model();
//        var_dump($model->php);
//        $model->name = 'long';
//        var_dump($model->name);
//        $model->getAttributeName();
//        $model->setAttributeLong(['long', 333]);
//        var_dump($model->getAttributeLong());

        $result = Db::table('test')->where('id', '=', $this->request->get()['id'])->query();
        return json_encode(['msg' => 'ok', 'code' => 0,'data' => $result]);
    }
}
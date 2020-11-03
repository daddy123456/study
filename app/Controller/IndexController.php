<?php

declare(strict_types=1);

namespace App\Controller;



use Src\Curl\Curl;

class IndexController
{
    public static function index()
    {
//        $url = 'http://api.btstu.cn/sjbz/api.php?format=json';
        $url = 'http://api.btstu.cn/qqinfo/api.php?qq=2227395155';
        $result = (new Curl($url))->get();
        var_dump($result);
        return true;
    }

    public function test()
    {
        echo 'Good good study, day day up', PHP_EOL;
        return true;
    }
}
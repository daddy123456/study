<?php

declare(strict_types=1);

namespace App\Controller;


class IndexController
{
    public static function index()
    {
//        $url = 'http://api.btstu.cn/sjbz/api.php?format=json';
        $url = 'http://api.btstu.cn/qqinfo/api.php?qq=2227395155';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        var_dump($response);
        return true;
    }

    public function test()
    {
        echo 'Good good study, day day up', PHP_EOL;
        return true;
    }
}
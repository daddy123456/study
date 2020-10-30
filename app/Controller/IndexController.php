<?php

declare(strict_types=1);

namespace App\Controller;


class IndexController
{
    const PRICE = 0.02;

    const BASE_PRICE = 200;

    public static function index()
    {
        $points = 10000;
        $money  = 200;
        $points1 = $points - ($money / self::PRICE);
        $discount = round(($points1 / $points)  * 10, 1);

        if ($discount) {
            $proportion = round(10 - $discount, 1);
            var_dump($proportion);
            var_dump('OR');
            var_dump('2-(points / (money / BASE_PRICE * (BASE_PRICE / PRICE)))');
        } else {
            var_dump('No discount');
        }
        return true;
    }

    public function test()
    {
        echo 'Good good study, day day up', PHP_EOL;
        return true;
    }
}
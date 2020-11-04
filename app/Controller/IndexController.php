<?php

declare(strict_types=1);

namespace App\Controller;


use App\AbstractController;
use Src\Curl\Curl;

class IndexController extends AbstractController
{
    public function index()
    {
        $url = '';
        $result = (new Curl($url))->get();
        var_dump($result);
        return true;
    }

    public function test()
    {
        var_dump($this->request->getUri());
        var_dump($this->request->get());
        return true;
    }
}
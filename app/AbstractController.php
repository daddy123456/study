<?php


namespace App;


use Src\Request\Request;
use Src\Request\Request as SwooleRequest;

abstract class AbstractController
{
    /** @var SwooleRequest */
    protected $request;

    public function __construct()
    {
        $this->request = new Request();
    }
}

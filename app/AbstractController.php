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

    public function success(string $msg, $data = [], $code = 0)
    {
        if (is_object($data)) {
            $data = (array) $data;
        }

        return json_encode(['code' => $code, 'msg' => $msg, 'data' => $data]);
    }

    public function error(string $msg, $code = 3001)
    {
        return json_encode(['code' => $code, 'error' => $msg, 'data' => []]);
    }
}

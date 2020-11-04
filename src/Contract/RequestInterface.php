<?php


namespace Src\Contract;


interface RequestInterface
{
    public function getMethod();

    public function getUri();

    public function getHeaders();

    public function getBody();

    public function post();

    public function get();
}
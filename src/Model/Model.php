<?php

declare(strict_types=1);

namespace Src\Model;


class Model
{
    /** @var array */
    protected $attribute = [];

    /**
     * @return array
     */
    public function getAttribute(string $name)
    {
        return $this->attribute[$name] ?? null;
    }

    /**
     * @param array $attribute
     */
    public function setAttribute(string $attribute, $value): void
    {
        $this->attribute[$attribute] = $value;
    }

    public function __get($name)
    {
        if (isset($this->attribute[$name])) {
            return $this->attribute[$name];
        }
        throw new \Exception('Property does not exist: ' . $name);
    }

    public function __set($name, $value)
    {
        $this->attribute[$name] = $value;
    }

    public function __call($name, $arguments)
    {
        $methodType = substr($name, 0,3);
        $attr = substr($name, 3, 9);
        $property = substr($name, 12);

        $method = $methodType . $attr;
        if (method_exists($this, $method)) {
            return call_user_func_array([$this, $method], array_merge([lcfirst($property)], $arguments));
        }
        throw new \Exception('Method does not exist: ' . $name);
    }
}

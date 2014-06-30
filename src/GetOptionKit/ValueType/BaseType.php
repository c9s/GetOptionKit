<?php
namespace GetOptionKit\ValueType;

abstract class BaseType
{
    public function __construct()
    {
        // code...
    }

    /**
     * Test a value to see if it fit the type
     *
     * @param mixed $value
     */
    abstract public function test($value);
}






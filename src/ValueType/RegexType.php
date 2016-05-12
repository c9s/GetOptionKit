<?php

namespace GetOptionKit\ValueType;

class RegexType extends BaseType
{
    public $matches = array();

    public function __construct($option)
    {
        $this->option = $option;
    }

    public function test($value)
    {
        return preg_match($this->option, $value) !== 0;
    }

    public function parse($value)
    {
        preg_match($this->option, $value, $this->matches);
        return strval($value);
    }
}

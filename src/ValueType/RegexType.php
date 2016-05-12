<?php

namespace GetOptionKit\ValueType;

class RegexType extends BaseType
{
    public $matches = [];

    public function test($value)
    {
        if (empty($this->option)) {
            return false;
        }
        $pm = preg_match($this->option, $value);
        return $pm !== 0;
    }

    public function parse($value)
    {
        // todo: match and return mached patterns
        // $pm = preg_match($this->option, $value);
        return strval($value);
    }
}

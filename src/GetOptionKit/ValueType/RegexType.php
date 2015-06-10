<?php
namespace GetOptionKit\ValueType;

class RegexType extends BaseType
{
    public $matches = [];

    public function test($value) { 
        $pm = preg_match( $this->option, $value);
        if($pm == 0) $pm = false;
        return $pm;
    }

    public function parse($value) {
        return strval($value);
    }
}



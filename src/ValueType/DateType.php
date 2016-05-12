<?php

namespace GetOptionKit\ValueType;

class DateType extends BaseType
{
    public function test($value)
    {
        $a = date_parse($value);
        if ($a === false || $a['error_count'] > 0) {
            return false;
        }
        return true;
    }

    public function parse($value)
    {
        return date_parse($value);
    }
}

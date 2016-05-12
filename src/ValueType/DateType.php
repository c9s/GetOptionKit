<?php
namespace GetOptionKit\ValueType;
use DateTime;

class DateType extends BaseType
{
    public function test($value) {
        $a = date_parse($value);
        if ($a === false) {
            return false;
        }
        return $a['error_count'] == 0;
    }

    public function parse($value) {
        return date_parse($value);
    }
}


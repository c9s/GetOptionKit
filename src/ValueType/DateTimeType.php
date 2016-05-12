<?php

namespace GetOptionKit\ValueType;

use DateTime;

class DateTimeType extends BaseType
{
    public $option = array(
        'format' => DateTime::ATOM,
    );

    public function test($value)
    {
        return DateTime::createFromFormat($this->option['format'], $value) !== false;
    }

    public function parse($value)
    {
        return DateTime::createFromFormat($this->option['format'], $value);
    }
}

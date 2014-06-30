<?php
use GetOptionKit\ValueType\BooleanType;
use GetOptionKit\ValueType\StringType;
use GetOptionKit\ValueType\FileType;
use GetOptionKit\ValueType\NumberType;

class ValueTypeTest extends PHPUnit_Framework_TestCase
{

    public function testTypeClass() 
    {
        ok( new BooleanType );
        ok( new StringType );
        ok( new FileType );
        ok( new NumberType );
    }


    public function testBooleanType()
    {
        $bool = new BooleanType;
        ok( $bool->test('true') );
        ok( $bool->test('false') );
        ok( $bool->test('0') );
        ok( $bool->test('1') );
    }

}


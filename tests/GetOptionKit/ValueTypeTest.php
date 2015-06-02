<?php
use GetOptionKit\ValueType\BooleanType;
use GetOptionKit\ValueType\StringType;
use GetOptionKit\ValueType\FileType;
use GetOptionKit\ValueType\NumberType;
use GetOptionKit\ValueType\UrlType;

class ValueTypeTest extends PHPUnit_Framework_TestCase
{

    public function testTypeClass() 
    {
        ok( new BooleanType );
        ok( new StringType );
        ok( new FileType );
        ok( new NumberType );
        ok( new UrlType );
    }


    public function testBooleanType()
    {
        $bool = new BooleanType;
        ok( $bool->test('true') );
        ok( $bool->test('false') );
        ok( $bool->test('0') );
        ok( $bool->test('1') );
    }

    public function testUrlType()
    {
        $url = new UrlType;
        ok( $url->test('http://t') );
        ok( $url->test('http://t.c') );
        $this->assertFalse($url->test('t.c'));
    }

}


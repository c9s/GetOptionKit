<?php

use GetOptionKit\ValueType\RegexType;

class RegexValueTypeTest extends \PHPUnit\Framework\TestCase
{
    public function testOption()
    {
        $regex = new RegexType('#^Test$#');
        $this->assertEquals($regex->option, '#^Test$#');
    }

    public function testValidation()
    {
        $regex = new RegexType('#^Test$#');
        $this->assertTrue($regex->test('Test'));
        $this->assertFalse($regex->test('test'));

        $regex->option = '/^([a-z]+)$/';
        $this->assertTrue($regex->test('barfoo'));
        $this->assertFalse($regex->test('foobar234'));
        $ret = $regex->parse('foobar234');
        $this->assertNotNull($ret);
    }
}


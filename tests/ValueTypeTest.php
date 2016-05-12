<?php
use GetOptionKit\ValueType\BooleanType;
use GetOptionKit\ValueType\StringType;
use GetOptionKit\ValueType\FileType;
use GetOptionKit\ValueType\NumberType;
use GetOptionKit\ValueType\UrlType;
use GetOptionKit\ValueType\IpType;
use GetOptionKit\ValueType\Ipv4Type;
use GetOptionKit\ValueType\Ipv6Type;
use GetOptionKit\ValueType\EmailType;
use GetOptionKit\ValueType\PathType;
use GetOptionKit\ValueType\DateType;
use GetOptionKit\ValueType\DateTimeType;

class ValueTypeTest extends PHPUnit_Framework_TestCase
{

    public function testTypeClass() 
    {
        ok( new BooleanType );
        ok( new StringType );
        ok( new FileType );
        ok( new DateType );
        ok( new DateTimeType );
        ok( new NumberType );
        ok( new UrlType );
        ok( new IpType );
        ok( new Ipv4Type );
        ok( new Ipv6Type );
        ok( new EmailType );
        ok( new PathType );
    }


    public function testDateTimeType()
    {
        $type = new DateTimeType([ 'format' => 'Y-m-d' ]);
        $this->assertTrue($type->test('2016-12-30'));
        $a = $type->parse('2016-12-30');
        $this->assertEquals(2016, $a->format('Y'));
        $this->assertEquals(12, $a->format('m'));
        $this->assertEquals(30, $a->format('d'));
        $this->assertFalse($type->test('foo'));
    }



    public function testDateType()
    {
        $type = new DateType;
        $this->assertTrue($type->test('2016-12-30'));
        $a = $type->parse('2016-12-30');
        $this->assertEquals(2016, $a['year']);
        $this->assertEquals(12, $a['month']);
        $this->assertEquals(30, $a['day']);
        $this->assertFalse($type->test('foo'));
    }



    public function booleanTestProvider()
    {
        return [
            ['true'  , true, true],
            ['false' , true, false], 
            ['0'     , true, false], 
            ['1'     , true, true], 
            ['foo'   , false, null], 
            ['123'   , false, null], 
        ];
    }

    /**
     * @dataProvider booleanTestProvider
     */
    public function testBooleanType($a, $test, $expected)
    {
        $bool = new BooleanType;
        $this->assertEquals($test, $bool->test($a));
        if ($bool->test($a)) {
            $this->assertEquals($expected, $bool->parse($a));
        }
    }

    public function testPathType()
    {
        $url = new PathType;
        $this->assertTrue($url->test('tests'));
        $this->assertTrue($url->test('composer.json'));
        $this->assertFalse($url->test('foo/bar'));
        $this->assertInstanceOf('SplFileInfo', $url->parse('composer.json'));
    }

    public function testUrlType()
    {
        $url = new UrlType;
        $this->assertTrue($url->test('http://t'));
        $this->assertTrue($url->test('http://t.c'));
        $this->assertFalse($url->test('t.c'));
        $this->assertEquals('http://t.c', $url->parse('http://t.c'));
    }

    public function testIpType()
    {
        $ip = new IpType;
        $this->assertTrue($ip->test('192.168.25.58'));
        $this->assertTrue($ip->test('2607:f0d0:1002:51::4'));
        $this->assertTrue($ip->test('::1'));
        $this->assertFalse($ip->test('10.10.15.10/16'));
        $this->assertEquals('10.10.15.10/16',$ip->parse('10.10.15.10/16'));
    }

    public function testIpv4Type()
    {
        $ipv4 = new Ipv4Type;
        ok($ipv4->test('192.168.25.58'));
        ok($ipv4->test('8.8.8.8'));
        $this->assertFalse($ipv4->test('2607:f0d0:1002:51::4'));
    }

    public function testIpv6Type()
    {
        $ipv6 = new Ipv6Type;
        ok( $ipv6->test('2607:f0d0:1002:51::4') );
        ok( $ipv6->test('2607:f0d0:1002:0051:0000:0000:0000:0004') );
        $this->assertFalse($ipv6->test('192.168.25.58'));
    }

    public function testEmailType()
    {
        $email = new EmailType;
        $this->assertTrue($email->test('test@gmail.com'));
        $this->assertFalse($email->test('test@test'));
        $email->parse('test@gmail.com');
    }
}


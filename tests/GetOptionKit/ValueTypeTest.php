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

class ValueTypeTest extends PHPUnit_Framework_TestCase
{

    public function testTypeClass() 
    {
        ok( new BooleanType );
        ok( new StringType );
        ok( new FileType );
        ok( new NumberType );
        ok( new UrlType );
        ok( new IpType );
        ok( new Ipv4Type );
        ok( new Ipv6Type );
        ok( new EmailType );
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

    public function testIpType()
    {
        $ip = new IpType;
        ok( $ip->test('192.168.25.58') );
        ok( $ip->test('2607:f0d0:1002:51::4') );
        ok( $ip->test('::1') );
        $this->assertFalse($ip->test('10.10.15.10/16'));
    }

    public function testIpv4Type()
    {
        $ipv4 = new Ipv4Type;
        ok( $ipv4->test('192.168.25.58') );
        ok( $ipv4->test('8.8.8.8') );
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
        ok( $email->test('test@gmail.com') );
        $this->assertFalse($email->test('test@test'));
    }
}


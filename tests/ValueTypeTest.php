<?php
use GetOptionKit\ValueType\BooleanType;
use GetOptionKit\ValueType\StringType;
use GetOptionKit\ValueType\FileType;
use GetOptionKit\ValueType\DirType;
use GetOptionKit\ValueType\NumberType;
use GetOptionKit\ValueType\UrlType;
use GetOptionKit\ValueType\IpType;
use GetOptionKit\ValueType\Ipv4Type;
use GetOptionKit\ValueType\Ipv6Type;
use GetOptionKit\ValueType\EmailType;
use GetOptionKit\ValueType\PathType;
use GetOptionKit\ValueType\DateType;
use GetOptionKit\ValueType\DateTimeType;
use GetOptionKit\ValueType\RegexType;

class ValueTypeTest extends \PHPUnit\Framework\TestCase
{

    public function testTypeClass() 
    {
        $this->assertNotNull( new BooleanType );
        $this->assertNotNull( new StringType );
        $this->assertNotNull( new FileType );
        $this->assertNotNull( new DateType );
        $this->assertNotNull( new DateTimeType );
        $this->assertNotNull( new NumberType );
        $this->assertNotNull( new UrlType );
        $this->assertNotNull( new IpType );
        $this->assertNotNull( new Ipv4Type );
        $this->assertNotNull( new Ipv6Type );
        $this->assertNotNull( new EmailType );
        $this->assertNotNull( new PathType );
        $this->assertNotNull( new RegexType("/[a-z]/"));
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
            [true  , true, true],
            [false , true, false], 
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

    public function testDirType()
    {
        $type = new DirType;
        $this->assertTrue($type->test('tests'));
        $this->assertFalse($type->test('composer.json'));
        $this->assertFalse($type->test('foo/bar'));
        $this->assertInstanceOf('SplFileInfo',$type->parse('tests'));
    }

    public function testFileType()
    {
        $type = new FileType;
        $this->assertFalse($type->test('tests'));
        $this->assertTrue($type->test('composer.json'));
        $this->assertFalse($type->test('foo/bar'));
        $this->assertInstanceOf('SplFileInfo', $type->parse('composer.json'));
    }

    public function testPathType()
    {
        $type = new PathType;
        $this->assertTrue($type->test('tests'));
        $this->assertTrue($type->test('composer.json'));
        $this->assertFalse($type->test('foo/bar'));
        $this->assertInstanceOf('SplFileInfo', $type->parse('composer.json'));
    }

    public function testUrlType()
    {
        $url = new UrlType;
        $this->assertTrue($url->test('http://t'));
        $this->assertTrue($url->test('http://t.c'));
        $this->assertFalse($url->test('t.c'));
        $this->assertEquals('http://t.c', $url->parse('http://t.c'));
    }

    public function ipV4Provider()
    {
        return [
            ['192.168.25.58', true],
            ['8.8.8.8', true],
            ['github.com', false],
        ];
    }

    public function ipV6Provider()
    {
        return [
            ['192.168.25.58', false],
            ['2607:f0d0:1002:51::4', true],
            ['2607:f0d0:1002:0051:0000:0000:0000:0004', true],
            ['::1', true],
            ['10.10.15.10/16', false],
            ['github.com', false],
        ];
    }

    public function ipProvider()
    {
        return [
            ['192.168.25.58', true],
            ['2607:f0d0:1002:51::4', true],
            ['::1', true],
            ['10.10.15.10/16', false],
            ['github.com', false],
        ];
    }

    /**
     * @dataProvider ipProvider
     */
    public function testIpType($ipstr, $pass = true)
    {
        $ip = new IpType;
        $this->assertEquals($pass, $ip->test($ipstr));
        if ($pass) {
            $this->assertNotNull($ip->parse($ipstr));
        }
    }

    /**
     * @dataProvider ipV4Provider
     */
    public function testIpv4Type($ipstr, $pass = true)
    {
        $ipv4 = new Ipv4Type;
        $this->assertEquals($pass, $ipv4->test($ipstr));
        if ($pass) {
            $this->assertNotNull($ipv4->parse($ipstr));
        }
    }

    /**
     * @dataProvider ipV6Provider
     */
    public function testIpv6Type($ipstr, $pass = true)
    {
        $ipv6 = new Ipv6Type;
        $this->assertEquals($pass, $ipv6->test($ipstr));
        if ($pass) {
            $this->assertNotNull($ipv6->parse($ipstr));
        }
    }

    public function testEmailType()
    {
        $email = new EmailType;
        $this->assertTrue($email->test('test@gmail.com'));
        $this->assertFalse($email->test('test@test'));
        $email->parse('test@gmail.com');
    }
}


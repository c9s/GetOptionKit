<?php
/*
 * This file is part of the GetOptionKit package.
 *
 * (c) Yo-An Lin <cornelius.howl@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
use GetOptionKit\InvalidOptionValue;
use GetOptionKit\OptionCollection;
use GetOptionKit\OptionParser;
use GetOptionKit\Option;

class OptionParserTest extends \PHPUnit\Framework\TestCase 
{
    public $parser;
    public $specs;

    public function setUp()
    {
        $this->specs = new OptionCollection;
        $this->parser = new OptionParser($this->specs);
    }

    /**
     * @expectedException Exception
     */
    public function testInvalidOption()
    {
        $options = new OptionCollection;
        $options->addOption(new Option(0));
    }


    public function testResultArrayAccessor()
    {
        $options = new OptionCollection;
        $options->add('n|nice:' , 'I take negative value');
        $parser = new OptionParser($options);
        $result = $parser->parse(array('a', '-n', '-1', '--', '......'));

        $this->assertTrue(isset($result->nice));
        $this->assertTrue($result->has('nice'));
        $this->assertTrue(isset($result['nice']));
        $this->assertEquals(-1, $result['nice']->value);

        $res = clone $result['nice'];
        $res->value = 10;
        $result['nice'] = $res;
        $this->assertEquals(10, $result['nice']->value);

        unset($result['nice']);
    }

    public function testCamelCaseOptionName()
    {
        $this->specs->add('base-dir:=dir' , 'I take path');
        $result = $this->parser->parse(array('a', '--base-dir', 'src'));
        $this->assertInstanceOf('SplFileInfo', $result->baseDir);
    }

    public function testOptionWithNegativeValue()
    {
        $this->specs->add('n|nice:' , 'I take negative value');
        $result = $this->parser->parse(array('a', '-n', '-1'));
        $this->assertEquals(-1, $result->nice);
    }

    public function testShortOptionName()
    {
        $this->specs->add('f:' , 'file');
        $result = $this->parser->parse(array('a', '-f', 'aaa'));
        $this->assertEquals('aaa',$result['f']->getValue());
    }

    public function testOptionWithShortNameAndLongName()
    {
        $this->specs->add( 'f|foo' , 'flag' );
        $result = $this->parser->parse(array('a', '-f'));
        $this->assertTrue($result->foo);

        $result = $this->parser->parse(array('a', '--foo'));
        $this->assertTrue($result->foo);
    }

    public function testSpec()
    {
        $options = new OptionCollection;
        $options->add( 'f|foo:' , 'option require value' );
        $options->add( 'b|bar+' , 'option with multiple value' );
        $options->add( 'z|zoo?' , 'option with optional value' );
        $options->add( 'v|verbose' , 'verbose message' );
        $options->add( 'd|debug'   , 'debug message' );
        $this->assertEquals(5, $options->size());
        $this->assertEquals(5, count($options));


        $opt = $options->get('foo');
        $this->assertTrue($opt->isRequired());

        $opt = $options->get('bar');
        $this->assertTrue( $opt->isMultiple() );

        $opt = $options->get('zoo');
        $this->assertTrue( $opt->isOptional() );

        $opt = $options->get( 'debug' );
        $this->assertNotNull( $opt );
        $this->assertInstanceOf('GetOptionKit\\Option', $opt);
        $this->assertEquals('debug', $opt->long);
        $this->assertEquals('d', $opt->short);
        $this->assertTrue($opt->isFlag());

        return $options;
    }

    /**
     * @depends testSpec
     */
    public function testOptionFinder($options)
    {
        $this->assertNotNull($options->find('f'));
        $this->assertNotNull($options->find('foo'));
        $this->assertNull($options->find('xyz'));
    }

    public function testRequire()
    {
        $this->specs->add( 'f|foo:' , 'option require value' );
        $this->specs->add( 'b|bar+' , 'option with multiple value' );
        $this->specs->add( 'z|zoo?' , 'option with optional value' );
        $this->specs->add( 'v|verbose' , 'verbose message' );
        $this->specs->add( 'd|debug'   , 'debug message' );

        $firstExceptionRaised = false;
        $secondExceptionRaised = false;

        // option required a value should throw an exception
        try {
            $result = $this->parser->parse( array('a', '-f' , '-v' , '-d' ) );
        }
        catch (Exception $e) {
            $firstExceptionRaised = true;
        }

        // even if only one option presented in args array
        try {
            $result = $this->parser->parse(array('a','-f'));
        } catch (Exception $e) {
            $secondExceptionRaised = true;
        }
        if ($firstExceptionRaised && $secondExceptionRaised) {
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    public function testMultiple()
    {
        $opt = new OptionCollection;
        $opt->add( 'b|bar+' , 'option with multiple value' );
        $parser = new OptionParser($opt);
        $result = $parser->parse(explode(' ','app -b 1 -b 2 --bar 3'));
        $this->assertNotNull($result->bar);
        $this->assertCount(3,$result->bar);
    }


    public function testMultipleNumber()
    {
        $opt = new OptionCollection;
        $opt->add('b|bar+=number' , 'option with multiple value');
        $parser = new OptionParser($opt);
        $result = $parser->parse(explode(' ','app --bar 1 --bar 2 --bar 3'));
        $this->assertNotNull($result->bar);
        $this->assertCount(3,$result->bar);
        $this->assertSame(array(1,2,3),$result->bar);
    }

    public function testSimpleOptionWithDefaultValue()
    {
        $opts = new OptionCollection;
        $opts->add('p|proc=number' , 'option with required value')
            ->defaultValue(10)
            ;
        $parser = new OptionParser($opts);
        $result = $parser->parse(explode(' ','app'));
        $this->assertEquals(10, $result['proc']->value);
    }

    public function testOptionalOptionWithDefaultValue()
    {
        $opts = new OptionCollection;
        $opts->add('p|proc?=number' , 'option with required value')
            ->defaultValue(10)
            ;
        $parser = new OptionParser($opts);
        $result = $parser->parse(explode(' ','app --proc'));
        $this->assertEquals(10, $result['proc']->value);
    }

    public function testMultipleString()
    {
        $opts = new OptionCollection;
        $opts->add('b|bar+=string' , 'option with multiple value');
        $bar = $opts->get('bar');
        $this->assertNotNull($bar);
        $this->assertTrue($bar->isMultiple());
        $this->assertTrue($bar->isType('string'));
        $this->assertFalse($bar->isType('number'));


        $parser = new OptionParser($opts);
        $result = $parser->parse(explode(' ','app --bar lisa --bar mary --bar john a b c'));
        $this->assertNotNull($result->bar);
        $this->assertCount(3,$result->bar);
        $this->assertSame(array('lisa', 'mary', 'john'),$result->bar);
        $this->assertSame(array('a','b','c'), $result->getArguments());
    }

    public function testParseIncrementalOption()
    {
        $opts = new OptionCollection;
        $opts->add('v|verbose' , 'verbose')
            ->isa("number")
            ->incremental();

        $parser = new OptionParser($opts);
        $result = $parser->parse(explode(' ','app -vvv arg1 arg2'));
        $this->assertInstanceOf('GetOptionKit\Option',$result['verbose']); 
        $this->assertNotNull($result['verbose']);
        $this->assertEquals(3, $result['verbose']->value);
    }


    /**
     * @expectedException Exception
     */
    public function testIntegerTypeNonNumeric()
    {
        $opt = new OptionCollection;
        $opt->add( 'b|bar:=number' , 'option with integer type' );

        $parser = new OptionParser($opt);
        $spec = $opt->get('bar');
        $this->assertTrue($spec->isTypeNumber());

        // test non numeric
        $result = $parser->parse(explode(' ','app -b test'));
        $this->assertNotNull($result->bar);
    }


    public function testIntegerTypeNumericWithoutEqualSign()
    {
        $opt = new OptionCollection;
        $opt->add('b|bar:=number', 'option with integer type');

        $spec = $opt->get('bar');
        $this->assertTrue($spec->isTypeNumber());

        $parser = new OptionParser($opt);
        $result = $parser->parse(explode(' ','app -b 123123'));
        $this->assertNotNull($result);
        $this->assertEquals(123123, $result->bar);
    }

    public function testIntegerTypeNumericWithEqualSign()
    {
        $opt = new OptionCollection;
        $opt->add('b|bar:=number' , 'option with integer type');

        $spec = $opt->get('bar');
        $this->assertTrue($spec->isTypeNumber());

        $parser = new OptionParser($opt);
        $result = $parser->parse(explode(' ','app -b=123123'));
        $this->assertNotNull($result);
        $this->assertNotNull($result->bar);
        $this->assertEquals(123123, $result->bar);
    }

    public function testStringType()
    {
        $this->specs->add( 'b|bar:=string' , 'option with type' );

        $spec = $this->specs->get('bar');

        $result = $this->parser->parse(explode(' ','app -b text arg1 arg2 arg3'));
        $this->assertNotNull($result->bar);

        $result = $this->parser->parse(explode(' ','app -b=text arg1 arg2 arg3'));
        $this->assertNotNull($result->bar);

        $args = $result->getArguments();
        $this->assertNotEmpty($args);
        $this->assertCount(3,$args);
        $this->assertEquals('arg1', $args[0]);
        $this->assertEquals('arg2', $args[1]);
        $this->assertEquals('arg3', $args[2]);
    }

    public function testStringQuoteOptionValue()
    {
        $opts = new OptionCollection();
        $opts->add('f|foo:' , 'option requires a value.');
        $parser = new OptionParser($opts);
        $res = $parser->parse(['app','--foo=aa bb cc']);
        $this->assertEquals('aa bb cc', $res->get('foo'));
    }

    public function testSpec2()
    {
        $this->specs->add('long'   , 'long option name only.');
        $this->specs->add('a'   , 'short option name only.');
        $this->specs->add('b'   , 'short option name only.');
        $this->assertNotNull($this->specs->all());
        $this->assertNotNull($this->specs);
        $this->assertNotNull($result = $this->parser->parse(explode(' ','app -a -b --long')) );
        $this->assertNotNull($result->a);
        $this->assertNotNull($result->b);
    }


    public function testSpecCollection()
    {
        $this->specs->add( 'f|foo:' , 'option requires a value.' );
        $this->specs->add( 'b|bar+' , 'option with multiple value.' );
        $this->specs->add( 'z|zoo?' , 'option with optional value.' );
        $this->specs->add( 'v|verbose' , 'verbose message.' );
        $this->specs->add( 'd|debug'   , 'debug message.' );
        $this->specs->add( 'long'   , 'long option name only.' );
        $this->specs->add( 's'   , 'short option name only.' );

        $this->assertNotNull( $this->specs->all() );
        $this->assertNotNull( $this->specs );

        $this->assertCount( 7 , $array = $this->specs->toArray() );
        $this->assertNotEmpty( isset($array[0]['long'] ));
        $this->assertNotEmpty( isset($array[0]['short'] ));
        $this->assertNotEmpty( isset($array[0]['desc'] ));
    }

    public function optionTestProvider()
    {
        return array(
            array( 'foo', 'simple boolean option', 'foo', true,
                [['a','--foo','a', 'b', 'c']]
            ),
            array( 'f|foo', 'simple boolean option', 'foo', true,
                [['a','--foo'], ['a','-f']] 
            ),
            array( 'f|foo:=string', 'string option', 'foo', 'xxx',
                [['a','--foo','xxx'], ['a','-f', 'xxx']] 
            ),
            array( 'f|foo:=string', 'string option', 'foo', 'xxx',
                [['a','b', 'c', '--foo','xxx'], ['a', 'a', 'b', 'c', '-f', 'xxx']] 
            ),
        );
    }

    /**
     * @dataProvider optionTestProvider
     */
    public function test($specString, $desc, $key, $expectedValue, array $argvList)
    {
        $opts = new OptionCollection();
        $opts->add($specString, $desc);
        $parser = new OptionParser($opts);
        foreach ($argvList as $argv) {
            $res = $parser->parse($argv);
            $this->assertSame($expectedValue, $res->get($key));
        }
    }

    /**
     * @expectedException Exception
     */
    public function testParseWithoutProgramName()
    {
        $parser = new OptionParser(new OptionCollection);
        $parser->parse(array('--foo'));
    }

    /**
     * @expectedException GetOptionKit\Exception\InvalidOptionException
     */
    public function testParseInvalidOptionException()
    {
        $parser = new OptionParser(new OptionCollection);
        $parser->parse(array('app','--foo'));
    }

    /**
     * @expectedException GetOptionKit\Exception\RequireValueException
     */
    public function testParseOptionRequireValueException()
    {
        $options = new OptionCollection;
        $options->add('name:=string', 'name');

        $parser = new OptionParser($options);
        $parser->parse(array('app','--name'));
    }



    public function testMore()
    {
        $this->specs->add('f|foo:' , 'option require value' );
        $this->specs->add('b|bar+' , 'option with multiple value' );
        $this->specs->add('z|zoo?' , 'option with optional value' );
        $this->specs->add('v|verbose' , 'verbose message' );
        $this->specs->add('d|debug'   , 'debug message' );

        $result = $this->parser->parse( array('a', '-f' , 'foo value' , '-v' , '-d' ) );
        $this->assertNotNull($result->foo);
        $this->assertNotNull($result->verbose);
        $this->assertNotNull($result->debug);
        $this->assertEquals( 'foo value', $result->foo );
        $this->assertNotNull( $result->verbose );
        $this->assertNotNull( $result->debug );

        foreach ($result as $k => $v) {
            $this->assertTrue(in_array($k, ['foo','bar','zoo','verbose', 'debug']));
            $this->assertInstanceOf('GetOptionKit\\Option', $v);
        }
        $this->assertSame([
            'foo' => 'foo value',
            'verbose' => true,
            'debug' => true
        ], $result->toArray());

        $result = $this->parser->parse( array('a', '-f=foo value' , '-v' , '-d' ) );
        $this->assertNotNull( $result );
        $this->assertNotNull( $result->foo );
        $this->assertNotNull( $result->verbose );
        $this->assertNotNull( $result->debug );

        $this->assertEquals( 'foo value', $result->foo );
        $this->assertNotNull( $result->verbose );
        $this->assertNotNull( $result->debug );

        $result = $this->parser->parse( array('a', '-vd' ) );
        $this->assertNotNull( $result->verbose );
        $this->assertNotNull( $result->debug );
    }

    public function testParseAcceptsValidOption()
    {
        $this->specs
            ->add('f:foo', 'test option')
            ->validator(function($value) {
                return $value === 'valid-option';
            });

        $result = $this->parser->parse(array('a', '-f' , 'valid-option'));

        $this->assertArrayHasKey('f', $result);
    }

    /**
     * @expectedException GetOptionKit\Exception\InvalidOptionValueException
     */
    public function testParseThrowsExceptionOnInvalidOption()
    {
        $this->specs
            ->add('f:foo', 'test option')
            ->validator(function($value) {
                return $value === 'valid-option';
            });

        $this->parser->parse(array('a', '-f' , 'not-a-valid-option'));
    }
}

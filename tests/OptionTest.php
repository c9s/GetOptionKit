<?php
use GetOptionKit\Option;

class OptionTest extends \PHPUnit\Framework\TestCase
{


    public function optionSpecDataProvider() {
        return [
            ['i'],
            ['f'],
            ['a=number'],

            // long options
            ['n|name'],
            ['e|email'],
        ];
    }


    /**
     * @dataProvider optionSpecDataProvider
     */
    public function testOptionSpec($spec)
    {
        $opt = new Option($spec);
        $this->assertNotNull($opt);
    }

    /**
     * @expectedException Exception
     */
    public function testInvalidOptionSpec()
    {
        new Option('....');
    }

    public function testValueName()
    {
        $opt = new Option('z');
        $opt->defaultValue(10);
        $opt->valueName('priority');
        $this->assertEquals('[=priority]', $opt->renderValueHint());
        $this->assertEquals('-z[=priority]', $opt->renderReadableSpec());
    }


    public function testDefaultValue()
    {
        $opt = new Option('z');
        $opt->defaultValue(10);
        $this->assertEquals(10, $opt->getValue());
        $this->assertEquals('-z[=10]',$opt->renderReadableSpec(true));
    }

    public function testBackwardCompatibleBoolean()
    {
        $opt = new Option('scope');
        $opt->isa('bool');
        $this->assertEquals('boolean', $opt->isa);
        $this->assertEquals('--scope=<boolean>',$opt->renderReadableSpec(true));
    }



    public function validatorProvider()
    {
        return [
            [function($a) { return in_array($a, ['public', 'private']); }],
            [function($a) { return [in_array($a, ['public', 'private']), "message"]; }]
        ];
    }

    /**
     * @dataProvider validatorProvider
     */
    public function testValidator($cb)
    {
        $opt = new Option('scope');
        $opt->validator($cb);
        $ret = $opt->validate('public');
        $this->assertTrue($ret[0]);
        $ret = $opt->validate('private');
        $this->assertTrue($ret[0]);
        $ret = $opt->validate('foo');
        $this->assertFalse($ret[0]);
        $this->assertEquals('--scope', $opt->renderReadableSpec(true));
    }

    /**
     * @expectedException Exception
     */
    public function testInvalidTypeClass()
    {
        $opt = new Option('scope');
        $opt->isa('SomethingElse');
        $class = $opt->getTypeClass();
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testValidatorReturnValue()
    {
        $opt = new Option('scope');
        $opt->validator(function($val) {
            return 123454;
        });
        $ret = $opt->validate('public');
    }

    public function testOptionWithoutValidator()
    {
        $opt = new Option('scope');
        $ret = $opt->validate('public');
        $this->assertTrue($ret[0]);
        $ret = $opt->validate('private');
        $this->assertTrue($ret[0]);
        $ret = $opt->validate('foo');
        $this->assertTrue($ret[0]);
        $this->assertEquals('--scope',$opt->renderReadableSpec(true));
    }




    public function testSuggestionsCallback()
    {
        $opt = new Option('scope');
        $this->assertEmpty($opt->getSuggestions());

        $opt->suggestions(function() {
            return ['public', 'private'];
        });
        $this->assertNotEmpty($opt->getSuggestions());
        $this->assertSame(['public', 'private'],$opt->getSuggestions());
        $opt->setValue('public');
        $opt->setValue('private');
        $this->assertEquals('private',$opt->value);

        $this->assertEquals('--scope=[public,private]',$opt->renderReadableSpec(true));
    }

    public function testSuggestions()
    {
        $opt = new Option('scope');
        $opt->suggestions(['public', 'private']);
        $this->assertNotEmpty($opt->getSuggestions());
        $this->assertSame(['public', 'private'],$opt->getSuggestions());
        $opt->setValue('public');
        $opt->setValue('private');
        $this->assertEquals('private',$opt->value);

        $this->assertEquals('--scope=[public,private]',$opt->renderReadableSpec(true));
    }

    public function testValidValuesCallback() {
        $opt = new Option('scope');
        $opt->validValues(function() {
            return ['public', 'private'];
        });
        $this->assertNotNull($opt->getValidValues());
        $this->assertNotEmpty($opt->getValidValues());

        $opt->setValue('public');
        $opt->setValue('private');
        $this->assertEquals('private',$opt->value);
        $this->assertEquals('--scope=(public,private)',$opt->renderReadableSpec(true));
    }

    public function testTrigger()
    {
        $opt = new Option('scope');
        $opt->validValues([ 'public', 'private' ]);

        $state = 0;
        $opt->trigger(function($val) use(& $state) {
            $state++;
        });
        $this->assertNotEmpty($opt->getValidValues());
        $opt->setValue('public');

        $this->assertEquals(1, $state);
        $opt->setValue('private');
        $this->assertEquals(2, $state);

    }



    public function testArrayValueToString()
    {
        $opt = new Option('uid');
        $opt->setValue([1,2,3,4]);
        $toString = '* key:uid      spec:--uid  desc:
  value => 1,2,3,4
';
        $this->assertEquals($toString,$opt->__toString());
    }

    public function testValidValues()
    {
        $opt = new Option('scope');
        $opt->validValues([ 'public', 'private' ])
            ;
        $this->assertNotEmpty($opt->getValidValues());
        $this->assertTrue(is_array($opt->getValidValues()));

        $opt->setValue('public');
        $opt->setValue('private');
        $this->assertEquals('private',$opt->value);
        $this->assertEquals('--scope=(public,private)',$opt->renderReadableSpec(true));
        $this->assertNotEmpty($opt->__toString());
    }


    public function testFilter() {
        $opt = new Option('scope');
        $opt->filter(function($val) { 
            return preg_replace('#a#', 'x', $val);
        })
        ;
        $opt->setValue('aa');
        $this->assertEquals('xx', $opt->value);
    }
}


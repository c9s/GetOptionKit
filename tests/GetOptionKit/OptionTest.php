<?php
use GetOptionKit\Option;

class OptionTest extends PHPUnit_Framework_TestCase
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
        ok($spec);
        $opt = new Option($spec);
        ok($opt);

    }

    public function testValidValues() {
        $opt = new Option('scope');
        $opt->validValues([ 'public', 'private' ])
            ;
        ok( $opt->getValidValues() );
        ok( is_array($opt->getValidValues()) );

        $opt->setValue('public');
        $opt->setValue('private');
        ok($opt->value);
        is('private',$opt->value);
    }
}


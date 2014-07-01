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
    public function testOption($spec)
    {
        ok($spec);
        $opt = new Option($spec);
        ok($opt);



    }
}


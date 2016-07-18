<?php
use GetOptionKit\Option;
use GetOptionKit\OptionCollection;

class OptionCollectionTest extends PHPUnit_Framework_TestCase
{
    public function testAddOption()
    {
        $opts = new OptionCollection;
        $opts->add($o = new Option('v|verbose'));
        $this->assertSame($o, $opts->getLongOption('verbose'));
        $this->assertSame($o, $opts->getShortOption('v'));
    }


    /**
     * @expectedException LogicException
     */
    public function testAddInvalidOption()
    {
        $opts = new OptionCollection;
        $opts->add(123);
    }
}

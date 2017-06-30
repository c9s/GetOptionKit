<?php
use GetOptionKit\Option;
use GetOptionKit\OptionCollection;

class OptionCollectionTest extends \PHPUnit\Framework\TestCase
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

    /**
     * @expectedException GetOptionKit\Exception\OptionConflictException
     */
    public function testOptionConflictShort()
    {
        $opts = new OptionCollection;
        $opts->add('r|repeat');
        $opts->add('t|time');
        $opts->add('r|regex');
    }

    /**
     * @expectedException GetOptionKit\Exception\OptionConflictException
     */
    public function testOptionConflictLong()
    {
        $opts = new OptionCollection;
        $opts->add('r|repeat');
        $opts->add('t|time');
        $opts->add('c|repeat');
    }
}

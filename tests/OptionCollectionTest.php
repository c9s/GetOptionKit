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

    public function testAddInvalidOption()
    {
        $this->expectException(\LogicException::class);

        $opts = new OptionCollection;
        $opts->add(123);
    }

    public function testOptionConflictShort()
    {
        $this->expectException(\GetOptionKit\Exception\OptionConflictException::class);

        $opts = new OptionCollection;
        $opts->add('r|repeat');
        $opts->add('t|time');
        $opts->add('r|regex');
    }

    public function testOptionConflictLong()
    {
        $this->expectException(\GetOptionKit\Exception\OptionConflictException::class);

        $opts = new OptionCollection;
        $opts->add('r|repeat');
        $opts->add('t|time');
        $opts->add('c|repeat');
    }
}

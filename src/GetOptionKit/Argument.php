<?php
/*
 * This file is part of the {{ }} package.
 *
 * (c) Yo-An Lin <cornelius.howl@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace GetOptionKit;

class Argument 
{
    public $arg;

    public function __construct($arg)
    {
        $this->arg = $arg;
    }


    public function isLongOption()
    {
        return substr($this->arg,0,2) === '--';
    }

    public function isShortOption()
    {
        return (substr($this->arg,0,1) === '-' ) 
            && (substr($this->arg,1,1) !== '-');
    }

    public function isEmpty()
    {
        return empty($this->arg) || strlen($this->arg) == 0;
    }



    /**
     * check current argument is an option
     *
     *   -a
     *   --foo
     */
    public function isOption()
    {
        return $this->isShortOption() || $this->isLongOption();
    }

    public function getOptionName()
    {
        if( preg_match('/^[-]+([a-zA-Z0-9-]+)/',$this->arg,$regs) ) {
            return $regs[1];
        }
    }

    public function splitAsOption() {
        return explode('=', $this->arg, 2);
    }

    public function containsOptionValue()
    {
        return preg_match('/=.+/',$this->arg);
    }

    public function getOptionValue()
    {
        if (preg_match('/=(.+)/',$this->arg,$regs)) {
            return $regs[1];
        }
    }

    /** 
     * check combined short flags
     *
     * like: -abc
     */
    public function withExtraFlagOptions()
    {
        return preg_match('/^-[a-zA-Z0-9]{2,}/',$this->arg);
    }

    public function extractExtraFlagOptions()
    {
        $args = array();
        for($i=2;$i< strlen($this->arg); ++$i) {
            $args[] = '-' . $this->arg[$i];
        }
        $this->arg = substr($this->arg,0,2); # -[a-z]
        return $args;
    }

    public function __toString()
    {
        return $this->arg;
    }

}




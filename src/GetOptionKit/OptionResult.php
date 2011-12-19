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
namespace GetOptionKit;
use Iterator;
use GetOptionKit\Argument;
use GetOptionKit\OptionSpec;

/* 
 * define the getopt parsing result
 *
 */
class OptionResult implements Iterator
{

    /* option specs , key => spec object */
    public $keys = array();

    private $currentKey;

    /* arguments */
    public $arguments = array();

    /* program name */
    public $program;

    function __construct()
    {

    }

    function __get($key)
    {
        if( isset($this->keys[ $key ]) )
            return @$this->keys[ $key ];
    }

    function __set($key,$value)
    {
        $this->keys[ $key ] = $value;
    }

    function set($key, OptionSpec $value)
    {
        $this->keys[ $key ] = $value;
    }


    function setProgram( $program )
    {
        $this->program = $program;
    }

    function addArgument( Argument $arg)
    {
        $this->arguments[] = $arg;
    }

    function getArguments()
    {
        return $this->arguments;
    }



    /* iterator methods */
    function rewind() 
    {
        return reset($this->keys);
    }

    function current() 
    {
        return current($this->keys);
    }

    function key() 
    {
        return key($this->keys);
    }

    function next() 
    {
        return next($this->keys);
    }

    function valid() 
    {
        return key($this->keys) !== null;
    }

}


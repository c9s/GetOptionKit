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


/* 
 * define the getopt parsing result
 *
 */
class OptionResult implements Iterator
{
    public $keys = array();
    private $currentKey;

    function __construct()
    {

    }

    function __get($key)
    {
        return @$this->keys[ $key ];
    }

    function __set($key,$value)
    {
        $this->keys[ $key ] = $value;
    }




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


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


/* 
 * define the getopt parsing result
 *
 */
class OptionResult
{
    function __construct()
    {

    }


    function __get($key)
    {
        return $this->keys[ $key ];
    }

    function __isset($key)
    {
        return isset( $this->keys[ $key ] );
    }

    function __set($key,$value)
    {
        $this->keys[ $key ] = $value;
    }


}



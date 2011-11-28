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
use GetOptionKit\NonNumericException;

class OptionSpec 
{
    public $short;
    public $long;
    public $description; /* description */
    public $key;  /* key to store values */
    public $value;
    public $type;

    const attr_multiple = 1;
    const attr_optional = 2;
    const attr_require  = 4;
    const attr_flag     = 8;

    const type_string   = 1;
    const type_integer  = 2;

    function __construct()
    {

    }

    function getId()
    {
        if( $this->key )
            return $this->key;
        if( $this->long )
            return $this->long;
        if( $this->short )
            return $this->short;
    }

    function setAttributeRequire()
    {
        $this->attributes = self::attr_require;
    }

    function setAttributeMultiple()
    {
        $this->attributes = self::attr_multiple;
        $this->value = array();  # for value pushing
    }

    function setAttributeOptional()
    {
        $this->attributes = self::attr_optional;
    }

    function setAttributeFlag()
    {
        $this->attributes = self::attr_flag;
    }


    function isAttributeFlag()
    {
        return $this->attributes & self::attr_flag;
    }

    function isAttributeMultiple()
    {
        return $this->attributes & self::attr_multiple;
    }

    function isAttributeRequire()
    {
        return $this->attributes & self::attr_require;
    }

    function isAttributeOptional()
    {
        return $this->attributes & self::attr_optional;
    }


    function setTypeString()
    {
        $this->type = self::type_string;
    }

    function setTypeInteger()
    {
        $this->type = self::type_integer;
    }

    function isTypeString()
    {
        return $this->type & self::type_string;
    }

    function isTypeInteger()
    {
        return $this->type & self::type_integer;
    }

    function checkType($value)
    {
        if( $this->type !== null ) {
            // check type constraints
            if( $this->isTypeInteger() ) {
                if( ! is_numeric($value) )
                    throw new NonNumericException;
                $value = (int) $value;
            }
        }
        return $value;
    }

    function setValue($value)
    {
        $value = $this->checkType($value);
        $this->value = $value;
    }

    function pushValue($value)
    {
        $value = $this->checkType($value);
        $this->value[] = $value;
    }

}



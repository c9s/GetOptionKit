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
use GetOptionKit\OptionSpec;

class GetOptionKit 
{
    public $specs;
    public $longOptions;
    public $shortOptions;

    function __construct()
    {
        $this->specs = array();
        $this->longOptions = array();
        $this->shortOptions = array();
    }

    function parseSpec($specString)
    {
        $pattern = '/
        ([a-zA-Z0-9]+)
        (?:\|([a-zA-Z0-9-]+))?
        ([:+?])?
        (?:=([si]))?
        /x';
        if( preg_match( $pattern, $specString , $regs ) ) {
            list($orig,$short,$long,$attributes,$type) = $regs;

            $spec = new OptionSpec;
            $spec->short = $short;
            $spec->long  = $long;

            if( strpos($attributes,':') !== false ) {
                $spec->setAttributeRequire();
            }
            elseif( strpos($attributes,'+') !== false ) {
                $spec->setAttributeMultiple();
            }
            elseif( strpos($attributes,'?') !== false ) {
                $spec->setAttributeOptional();
            } 
            else {
                $spec->setAttributeFlag();
            }
            return $spec;
        }
        else {
            throw new Exception( "Unknown spec string" );
        }
    }

    function add( $spec, $description , $key = null ) 
    {
        // parse spec
        $spec = $this->parseSpec($spec);
        $spec->description = $description;
        $spec->key = $key;
        $this->specs[ $spec->getId() ] = $spec;

        if( $spec->long )
            $this->longOptions[ $spec->long ] = $spec;
        elseif( $spec->short )
            $this->longOptions[ $spec->short ] = $spec;
        else
            throw new Exception;
    }


    /* get spec by spec id */
    function get($id)
    {
        return @$this->specs[ $id ];
    }

    function parse($argv)
    {
        $len = count($argv);
        for( $i = 0; $i < $len; ++$i ) {

        }
    }


}




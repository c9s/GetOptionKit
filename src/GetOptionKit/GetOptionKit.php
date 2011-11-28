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
use GetOptionKit\OptionSpecCollection;
use GetOptionKit\OptionResult;
use GetOptionKit\OptionParser;
use Exception;

class GetOptionKit 
{
    function __construct()
    {
        $this->specs = new OptionSpecCollection;
    }


    function parseSpec($specString)
    {
        $pattern = '/
        ([a-zA-Z0-9]+)
        (?:\|([a-zA-Z0-9-]+))?
        ([:+?])?
        (?:=([si]|string|integer))?
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

            if( $type ) {
                if( $type === 's' || $type === 'string' ) {
                    $spec->setTypeString();
                }
                elseif( $type === 'i' || $type === 'integer' ) {
                    $spec->setTypeInteger();
                }
            }

            return $spec;
        }
        else {
            throw new Exception( "Unknown spec string" );
        }
    }


    /* add option specification from string spec 
     *
     * @param $spec string
     * @param $description string
     * @param $key
     *
     * */
    function add( $spec, $description , $key = null ) 
    {
        // parse spec
        $spec = $this->parseSpec($spec);
        $spec->description = $description;
        $spec->key = $key;
        $this->specs->add( $spec );
        return $spec;
    }

    /* get option specification by Id */
    function get($id)
    {
        return $this->specs->get($id);
    }

    /* get all option specification */
    function getSpecs()
    {
        return $this->specs;
    }

    function parse( $argv ) 
    {
        $parser = new OptionParser( $this->specs->data );
        return $parser->parse( $argv );
    }

    function printSpecs( GetOptionKit\OptionPrinterInterface $class = 'GetOptionKit\OptionPrinter')
    {
        $printer = new $class;

    }
}


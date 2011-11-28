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
use GetOptionKit\OptionResult;

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


    function getLongOptionSpec( $name )
    {
        return @$this->longOptions[ $name ];
    }

    function getShortOptionSpec( $name )
    {
        return @$this->shortOptions[ $name ];
    }


    /* get spec by spec id */
    function get($id)
    {
        return @$this->specs[ $id ];
    }

    function isOption($arg)
    {
        return substr($arg,0,1) === '-';
    }

    function parse($argv)
    {
        $result = new OptionResult;
        $len = count($argv);
        for( $i = 0; $i < $len; ++$i ) {
            $arg = new Argument( $argv[$i] );
            if( $arg->isLongOption() ) {
                $spec = $this->getLongOptionSpec($arg->getOptionName());
                if( $spec->isAttributeRequire() ) {
                    $i++;
                    $nextArgument = new Argument( $args[$i] );
                    if( $nextArgument->isOption() )
                        throw new Exception( "option {$arg->getOptionName()} require a value." );
                }
                elseif( $spec->isAttributeMultiple() ) {

                }
                elseif( $spec->isAttributeOptional() ) {

                }
                elseif( $spec->isAttributeFlag() ) {

                }
            }
            elseif( $arg->isShortOption() ) {
                $spec = $this->getShortOptionSpec($arg->getOptionName());

            }

        }
    }


}




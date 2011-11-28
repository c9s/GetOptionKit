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
use Exception;

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
        if( $spec->short )
            $this->longOptions[ $spec->short ] = $spec;
        if( ! $spec->long && ! $spec->short )
            throw new Exception('Wrong option spec');
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

    function getSpec($name)
    {
        if( isset($this->longOptions[ $name ] ))
            return $this->longOptions[ $name ];
        if( isset($this->shortOptions[ $name ] ))
            return $this->shortOptions[ $name ];
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
            if( ! $arg->isOption() )
                continue;

            $next = new Argument( $args[$i] );
            $spec = $this->getSpec( $arg->getOptionName() );
            if( ! $spec )
                throw new Exception("invalid option: " . $arg );

            if( $spec->isAttributeRequire() ) {
                /* argument doesn't contain value and next argument is not option */
                if( ! $arg->containsOptionValue() && $next->isOption() )
                    throw new Exception( "option {$arg->getOptionName()} require a value." );

                if( $arg->containsOptionValue() )
                    $spec->value = $arg->getOptionValue();
                else
                    $spec->value = $next->arg;
                $i++;
                $result->{ $spec->getId() } = $spec;
            }
            elseif( $spec->isAttributeMultiple() ) {

            }
            elseif( $spec->isAttributeOptional() ) {

            }
            elseif( $spec->isAttributeFlag() ) {
                $spec->value = true;
                $result->{ $spec->getId() } = $spec;
            }
        }
        return $result;
    }


}




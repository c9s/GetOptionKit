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
use GetOptionKit\Argument;
use Exception;

class OptionParser 
{
    public $specs;
    public $longOptions;
    public $shortOptions;

    function __construct($specs = array())
    {
        $this->specs = $specs;
        $this->longOptions = array();
        $this->shortOptions = array();
        foreach( $specs as $spec ) {
            if( $spec->long )
                $this->longOptions[ $spec->long ] = $spec;
            if( $spec->short )
                $this->longOptions[ $spec->short ] = $spec;
            if( ! $spec->long && ! $spec->short )
                throw new Exception('Wrong option spec');
        }
    }

    function getLongOption( $name )
    {
        return @$this->longOptions[ $name ];
    }

    function getShortOption( $name )
    {
        return @$this->shortOptions[ $name ];
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

    function takeOptionValue($spec,$arg,$next)
    {
        if( $arg->containsOptionValue() ) {
            $spec->setValue( $arg->getOptionValue() );
        }
        elseif( ! $next->isOption() )  {
            $spec->setValue( $next->arg );
        }
    }

    function pushOptionValue($spec,$arg,$next)
    {
        if( $arg->containsOptionValue() )
            $spec->pushValue( $arg->getOptionValue() );
        elseif( ! $next->isOption() ) 
            $spec->pushValue( $next->arg );
    }

    function checkValue($spec,$arg,$next)
    {
        if( ! $next )
            return false;

        /* argument doesn't contain value and next argument is option */
        return ( ! $arg->containsOptionValue() 
                            && $next->isOption() );
    }

    function parse($argv)
    {
        $result = new OptionResult;
        $len = count($argv);
        $result->setProgram( $argv[0] );

        for( $i = 1; $i < $len; ++$i ) 
        {
            $arg = new Argument( $argv[$i] );
            if( ! $arg->isOption() ) {
                $result->addArgument( $arg );
                continue;
            }

            // if the option is with extra flags,
            //   split it out, and insert into the argv array
            if( $arg->withExtraFlagOptions() ) {
                $extra = $arg->extractExtraFlagOptions();
                array_splice( $argv, $i+1, 0, $extra );
                $argv[$i] = $arg->arg; // update argument to current argv list.
                $len = count($argv);   // update argv list length
            }
            

            $next = new Argument( $argv[$i + 1] );
            $spec = $this->getSpec( $arg->getOptionName() );
            if( ! $spec )
                throw new Exception("Invalid option: " . $arg );


            if( $spec->isAttributeRequire() ) {

                if( $this->checkValue($spec,$arg,$next) )
                    throw new Exception( "Option {$arg->getOptionName()} require a value." );

                $this->takeOptionValue($spec,$arg,$next);
                if( ! $next->isOption() )
                    $i++;
                $result->set($spec->getId(), $spec);
            }
            elseif( $spec->isAttributeMultiple() ) {
                $this->pushOptionValue($spec,$arg,$next);
                if( $next->isOption() )
                    $i++;
                $result->set( $spec->getId() , $spec);
            }
            elseif( $spec->isAttributeOptional() ) {
                $this->takeOptionValue($spec,$arg,$next);
                if( $spec->value && ! $next->isOption() )
                    $i++;
                $result->set( $spec->getId() , $spec);
            }
            elseif( $spec->isAttributeFlag() ) {
                $spec->value = true;
                $result->set( $spec->getId() , $spec);
            }
            else {
                throw new Exception('Unknown attribute.');
            }
        }
        return $result;
    }
}

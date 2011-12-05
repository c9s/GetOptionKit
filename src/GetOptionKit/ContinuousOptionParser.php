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
use GetOptionKit\OptionParser;
use Exception;

/* 
 * when parser meets arguments, 
 * parser should take the arguments, save current index, and return the option result.
 * when next parsing started, parser should start from last index, and return another option result.
 *
 * application is able to use isEnd() method to check the parser status.
 * */
class ContinuousOptionParser extends OptionParser
{
    public $index;
    public $length;
    public $argv;

    function __construct($specs = array())
    {
        parent::__construct($specs);
        $this->index = 1;
    }

    function startFrom($index)
    {
        $this->index = $index;
    }

    function isEnd()
    {
        # echo "!! {$this->index} >= {$this->length}\n";
        return ($this->index >= $this->length);
    }

    function continueParse()
    {
        return $this->parse( $this->argv );
    }

    function parse($argv)
    {
        // create new Result object.
        $result = new OptionResult;
        $this->argv = $argv;
        $this->length = count($argv);
        if( $this->isEnd() )
            return;

        $result->setProgram( $argv[0] );
        $gotArguments = false;

        // from last parse index
        for( ; $this->index < $this->length; ++$this->index ) 
        {
            $arg = new Argument( $argv[$this->index] );
            if( ! $arg->isOption() ) {
                $result->addArgument( $arg );
                $gotArguments = true;
                continue;
            }
            elseif( $gotArguments ) {
                return $result;
            }


            // if the option is with extra flags,
            //   split it out, and insert into the argv array
            if( $arg->withExtraFlagOptions() ) {
                $extra = $arg->extractExtraFlagOptions();
                array_splice( $argv, $this->index +1, 0, $extra );
                $argv[$this->index] = $arg->arg; // update argument to current argv list.
                $len = count($argv);   // update argv list length
            }

            $next = new Argument( $argv[$this->index + 1] );
            $spec = $this->specs->getSpec( $arg->getOptionName() );
            if( ! $spec )
                throw new Exception("Invalid option: " . $arg );

            if( $spec->isAttributeRequire() ) 
            {

                if( $this->checkValue($spec,$arg,$next) )
                    throw new Exception( "Option {$arg->getOptionName()} require a value." );

                $this->takeOptionValue($spec,$arg,$next);
                if( ! $next->isOption() )
                    $this->index++;
                $result->set($spec->getId(), $spec);
            }
            elseif( $spec->isAttributeMultiple() ) 
            {
                $this->pushOptionValue($spec,$arg,$next);
                if( $next->isOption() )
                    $this->index++;
                $result->set( $spec->getId() , $spec);
            }
            elseif( $spec->isAttributeOptional() ) 
            {
                $this->takeOptionValue($spec,$arg,$next);
                if( $spec->value && ! $next->isOption() )
                    $this->index++;
                $result->set( $spec->getId() , $spec);
            }
            elseif( $spec->isAttributeFlag() ) 
            {
                $spec->value = true;
                $result->set( $spec->getId() , $spec);
            }
            else 
            {
                throw new Exception('Unknown attribute.');
            }
        }
        return $result;
    }


}

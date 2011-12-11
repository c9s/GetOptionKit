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

/**
 * A common command line argument format:
 *
 *      app.php
 *         [--app-options]
 *
 *      [subcommand
 *          --subcommand-options]
 *      [subcommand
 *          --subcommand-options]
 *      [subcommand
 *          --subcommand-options]
 *
 *      [arguments]
 *
 * ContinuousOptionParser is for the process flow:
 *
 * init app options,
 * parse app options
 * 
 * if stop,
 * if stop at command
 *    shift command
 *    parse command options
 *
 *    if stop
 *      if stop at command
 *        shift command
 *        init command options
 *        parse command options
 *      if stop at arguments
 *        shift arguments
 *        execute current command with the arguments.
 *
 * */
class ContinuousOptionParser extends OptionParser
{
    public $index;
    public $length;
    public $argv;

    /* for the constructor , the option specs is application options */
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

    function advance()
    {
        $arg = $this->argv[ $this->index++ ];
        return $arg;
    }

    function getCurrentArgument()
    {
        return $this->argv[ $this->index ];
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

        // from last parse index
        for( ; $this->index < $this->length; ++$this->index ) 
        {
            $arg = new Argument( $argv[$this->index] );

            /* let the application decide for: command or arguments */
            if( ! $arg->isOption() ) {
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

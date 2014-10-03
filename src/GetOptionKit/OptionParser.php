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
use GetOptionKit\Option;
use GetOptionKit\OptionCollection;
use GetOptionKit\OptionResult;
use GetOptionKit\Argument;
use Exception;
use GetOptionKit\Exception\InvalidOptionException;
use GetOptionKit\Exception\RequireValueException;

class OptionParser 
{
    public $specs;
    public $longOptions;
    public $shortOptions;

    public function __construct(OptionCollection $specs)
    {
        $this->specs = $specs;
    }

    public function setSpecs(OptionCollection $specs)
    {
        $this->specs = $specs;
    }


    /* a helper to build option specification object from string spec 
     *
     * @param $specString string
     * @param $description string
     * @param $key
     *
     * */
    public function addSpec( $specString, $description , $key = null ) 
    {
        $spec = $this->specs->add($specString,$description,$key);
        return $spec;
    }

    /* get option specification by Id */
    public function getSpec($id)
    {
        return $this->specs->get($id);
    }




    /* detect option */
    public function isOption($arg)
    {
        return substr($arg,0,1) === '-';
    }

    /* take option value from current argument or from the next argument */
    public function takeOptionValue($spec,$arg,$next)
    {
        if( $arg->containsOptionValue() ) {
            $spec->setValue( $arg->getOptionValue() );
        }
        elseif( $next && ! $next->isOption() )  {
            $spec->setValue( $next->arg );
        }
	    else {
		    $spec->setValue(true);
	    }
    }

    /* 
     * push value to multipl value option
     */
    public function pushOptionValue(Option $spec,$arg,$next)
    {
        if ($arg->containsOptionValue()) {
            $spec->pushValue( $arg->getOptionValue() );
        } elseif( ! $next->isOption() ) {
            $spec->pushValue( $next->arg );
        }
    }

    public function foundRequireValue($spec,$arg,$next)
    {
        /* argument doesn't contain value and next argument is option */
        if ($arg->containsOptionValue()) {
            return true;
        }

        if (! $arg->containsOptionValue() && $next && ! $next->isEmpty() && ! $next->isOption()) {
            return true;
        }

        return false;
    }


    public function parse(array $argv)
    {
        $result = new OptionResult;
        $len = count($argv);
        for( $i = 0; $i < $len; ++$i ) 
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

            $next = new Argument( @$argv[$i + 1] );
            $spec = $this->specs->get( $arg->getOptionName() );
            if (! $spec) {
                throw new InvalidOptionException("Invalid option: " . $arg );
            }

            if ($spec->isRequired())
            {
                if ( ! $this->foundRequireValue($spec,$arg,$next) ) {
                    throw new RequireValueException( "Option {$arg->getOptionName()} require a value." );
                }

                $this->takeOptionValue($spec,$arg,$next);
                if( ! $next->isOption() )
                    $i++;
                $result->set($spec->getId(), $spec);
            }
            elseif( $spec->isMultiple() ) 
            {
                $this->pushOptionValue($spec,$arg,$next);
                if( $next->isOption() )
                    $i++;
                $result->set( $spec->getId() , $spec);
            }
            elseif( $spec->isOptional() ) 
            {
                $this->takeOptionValue($spec,$arg,$next);
                if( $spec->value && ! $next->isOption() )
                    $i++;
                $result->set( $spec->getId() , $spec);
            }
            elseif( $spec->isFlag() ) 
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

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

    /* a helper to build option specification object from string spec 
     *
     * @param $specString string
     * @param $description string
     * @param $key
     *
     * */
    function add( $specString, $description , $key = null ) 
    {
        $spec = $this->specs->addFromSpecString($specString);
        $spec->description = $description;
        if( $key )
            $spec->key = $key;
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

    function printOptions( $class = 'GetOptionKit\OptionPrinter' )
    {
        $printer = new $class( $this->specs );
        if( !( $printer instanceof \GetOptionKit\OptionPrinterInterface )) {
            throw new Exception("$class does not implement GetOptionKit\OptionPrinterInterface.");
        }
        $printer->printOptions();
    }
}


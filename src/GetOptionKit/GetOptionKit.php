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

class GetOptionKit 
{
    public $options;
    public $descriptions;

    const OPT_MULTIPLE = 1;
    const OPT_OPTIONAL = 2;
    const OPT_REQUIRE  = 4;
    const OPT_FLAG     = 8;

    function __construct()
    {
        $this->options = array();
        $this->descriptions = array();
    }

    function parseSpec($spec)
    {
        $options = 0;

        $pattern = '/
        ([a-zA-Z0-9]+)
        (?:([a-zA-Z0-9-]+))?
        ([:+?])?
        (=[si])?
        /x';
        if( preg_match( $pattern, $spec , $regs ) ) {
            list($short,$long,$options,$type) = $regs;


        }
        else {
            throw new Exception( "Unknown spec string" );
        }
    }

    function add( $spec, $description , $key = null ) 
    {
        // parse spec
        $this->parseSpec($spec);

    }

    function parse($argv)
    {
        $len = count($argv);
        for( $i = 0; $i < $len; ++$i ) {

        }
    }


}




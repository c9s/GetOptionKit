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
class GetOptionKitTest extends PHPUnit_Framework_TestCase 
{

    function test()
    {
        $opt = new \GetOptionKit\GetOptionKit;
        ok( $opt );

        $opt->add( 'v|verbose' , 'verbose message' , 'verbose' );
        $opt->add( 'd|debug'   , 'debug message' , 'debug' );

        $result = $opt->parse( array( 'program' , '-v' , '-d' ) );

        ok( $result );
        ok( $result->verbose );
        ok( $result->debug );
    }


}

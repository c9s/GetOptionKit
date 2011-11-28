<?php
/*
 * This file is part of the {{ }} package.
 *
 * (c) Yo-An Lin <cornelius.howl@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

use GetOptionKit\Argument;
class ArgumentTest extends PHPUnit_Framework_TestCase 
{
    function test()
    {
        $arg = new Argument( '--option' );
        ok( $arg->isLongOption() );
        not_ok( $arg->isShortOption() );
        is( 'option' , $arg->getOptionName() );
    }

    function test2()
    {
        $arg = new Argument( '--option=value' );
        ok( $arg->containsOptionValue() );
        is( 'value' , $arg->getOptionValue() );
        is( 'option' , $arg->getOptionName() );
    }


}



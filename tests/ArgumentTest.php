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
class ArgumentTest extends \PHPUnit\Framework\TestCase 
{
    function test()
    {
        $arg = new Argument( '--option' );
        $this->assertTrue( $arg->isLongOption() );
        $this->assertFalse( $arg->isShortOption() );
        $this->assertEquals('option' , $arg->getOptionName());

        $this->assertEquals(null, $arg->getOptionValue());
    }

    function test2()
    {
        $arg = new Argument('--option=value');
        $this->assertNotNull( $arg->containsOptionValue() );
        $this->assertEquals('value' , $arg->getOptionValue());
        $this->assertEquals('option' , $arg->getOptionName());
    }

    function test3()
    {
        $arg = new Argument( '-abc' );
        $this->assertNotNull( $arg->withExtraFlagOptions() );

        $args = $arg->extractExtraFlagOptions();
        $this->assertNotNull( $args );
        $this->assertCount( 2, $args );

        $this->assertEquals( '-b', $args[0] );
        $this->assertEquals( '-c', $args[1] );
        $this->assertEquals( '-a', $arg->arg);
    }

    function testZeroValue()
    {
        $arg = new Argument( '0' );
        $this->assertFalse( $arg->isShortOption() );
        $this->assertFalse( $arg->isLongOption() );
        $this->assertFalse( $arg->isEmpty() );
    }
}



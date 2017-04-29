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

class OptionResultTest extends \PHPUnit\Framework\TestCase 
{

    function testOption()
    {
        $option = new \GetOptionKit\OptionResult;
        $this->assertNotNull( $option );

        $specs = new \GetOptionKit\OptionCollection;
        $specs->add('name:','name');
        $result = \GetOptionKit\OptionResult::create($specs,array( 'name' => 'c9s' ),array( 'arg1' ));
        $this->assertNotNull( $result );
        $this->assertNotNull( $result->arguments );
        $this->assertNotNull( $result->name );
        $this->assertEquals( 'c9s', $result->name );
        $this->assertEquals( $result->arguments[0] , 'arg1' );
    }

}



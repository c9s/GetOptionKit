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

class OptionResultTest extends PHPUnit_Framework_TestCase 
{

    function testOption()
    {
        $option = new \GetOptionKit\OptionResult;
        ok( $option );

    }

}



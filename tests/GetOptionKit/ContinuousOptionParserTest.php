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

namespace tests\GetOptionKit;
use GetOptionKit\ContinuousOptionParser;
use GetOptionKit\OptionSpecCollection;

class ContinuousOptionParserTest extends \PHPUnit_Framework_TestCase 
{
    function testParser() 
    {
        $specs = new OptionSpecCollection;
        $spec_verbose = $specs->addFromSpecString('v|verbose');
        $spec_color = $specs->addFromSpecString('c|color');
        $spec_debug = $specs->addFromSpecString('d|debug');

        ok( $spec_verbose );
        ok( $spec_color );
        ok( $spec_debug );
        ok( $specs );
        // $parser = new ContinuousOptionParser();
    }

    function testParser2()
    {
        $specs = new OptionSpecCollection;
        $spec_verbose = $specs->addFromSpecString('v|verbose');
        $spec_color = $specs->addFromSpecString('c|color');
        $spec_debug = $specs->addFromSpecString('d|debug');

        $parser = new ContinuousOptionParser;
        ok( $parser );
    }
}


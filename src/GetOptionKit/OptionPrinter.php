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
use GetOptionKit\OptionSpecCollection;

class OptionPrinter implements OptionPrinterInterface
{
    public $specs;

    function __construct( OptionSpecCollection $specs)
    {
        $this->specs = $specs;
    }

    function printOptions()
    {
        echo "* Available options:\n";
        foreach( $this->specs->all() as $spec ) 
        {
            $line = str_repeat(' ',4);
            if( $spec->short && $spec->long )
                $line = $spec->short . ", " . $spec->long;
            elseif( $spec->short )
                $line = $spec->short;
            elseif( $spec->long )
                $line = $spec->long;

            if( strlen($line) > 25 ) {
                $line .= "\n";
                $line .= str_repeat(26);
                $line .= $spec->description;  # wrap text
            } else {
                $line = sprintf('% 26s %s',$line, $spec->description );
            }
            echo $line . "\n";
        }
    }
}

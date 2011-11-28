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
            $c1 = '';
            if( $spec->short && $spec->long )
                $c1 = sprintf('-%s, --%s',$spec->short,$spec->long);
            elseif( $spec->short )
                $c1 = sprintf('-%s',$spec->short);
            elseif( $spec->long )
                $c1 = sprintf('--%s',$spec->long );

            if( $spec->isAttributeRequire() ) {
                $c1 .= ' <value>';
            }
            elseif( $spec->isAttributeMultiple() ) {
                $c1 .= ' <value>';
            }
            elseif( $spec->isAttributeOptional() ) {
                $c1 .= ' [<value>]';
            }
            elseif( $spec->isAttributeFlag() ) {
            }


            if( strlen($c1) > 24 ) {
                $line = sprintf('% 24s', $c1) . "\n" . str_repeat(26) . $spec->description;  # wrap text
            } else {
                $line = sprintf('% 24s   %s',$c1, $spec->description );
            }
            echo $line . "\n";
        }
    }
}

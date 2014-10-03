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
use GetOptionKit\OptionCollection;
use GetOptionKit\Option;

class OptionPrinter implements OptionPrinterInterface
{
    public $specs;

    public function __construct(OptionCollection $specs)
    {
        $this->specs = $specs;
    }


    /**
     * get readable spec for printing
     *
     */
    public function renderOptionSpec(Option $opt)
    {
        $c1 = '';
        if ( $opt->short && $opt->long ) {
            $c1 = sprintf('-%s, --%s',$opt->short,$opt->long);
        } elseif( $opt->short ) {
            $c1 = sprintf('-%s',$opt->short);
        } elseif( $opt->long ) {
            $c1 = sprintf('--%s',$opt->long );
        }
        $c1 .= $opt->renderValueHint();
        return $c1;
    }



    /**
     * render option descriptions
     *
     * @param integer $width column width
     * @return string output
     */
    public function outputOptions($width = 24)
    {
        # echo "* Available options:\n";
        $lines = array();
        foreach( $this->specs->all() as $spec ) 
        {
            $c1 = $this->renderOptionSpec($spec);
            $line = "\t" . $c1 . "\n\t\t" . wordwrap($spec->desc, 75, "\n\t\t") . "\n";  # wrap text
            /*
            if( strlen($c1) > $width ) {
                $line = sprintf("% {$width}s", $c1) . "\n" . $spec->desc;  # wrap text
            } else {
                $line = sprintf("% {$width}s   %s",$c1, $spec->desc );
            }
             */
            $lines[] = $line;
        }
        return $lines;
    }

    /**
     * print options descriptions to stdout
     *
     */
    public function printOptions()
    {
        $lines = $this->outputOptions();
        echo join( "\n" , $lines );
    }
}

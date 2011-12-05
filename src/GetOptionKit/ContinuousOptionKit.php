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
use GetOptionKit\OptionSpec;
use GetOptionKit\OptionSpecCollection;
use GetOptionKit\OptionResult;
use GetOptionKit\OptionParser;
use GetOptionKit\ContinuousOptionParser;
use Exception;

/* A wrapper class for continuous option parser */
class ContinuousOptionKit extends GetOptionKit
{
    public $parser;

    function parse( $argv ) 
    {
        return $this->parser->parse( $argv );
    }

    function continueParse()
    {
        return $this->parser->continueParse();
    }
}


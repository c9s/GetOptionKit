#!/usr/bin/env php
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
require 'vendor/autoload.php';

use GetOptionKit\GetOptionKit;
$opt = new GetOptionKit;
$opt->add( 'f|foo:' , 'option requires a value.' )
    ->is('string');

$opt->add( 'b|bar+' , 'option with multiple value.' )
    ->is('number');

$opt->add( 'z|zoo?' , 'option with optional value.' )
    ->is('boolean');

$opt->add( 'file:' , 'option value should be a file.' )
    ->is('file');

$opt->add( 'v|verbose' , 'verbose message.' );
$opt->add( 'd|debug'   , 'debug message.' );
$opt->add( 'long'   , 'long option name only.' );
$opt->add( 's'   , 'short option name only.' );
$opt->specs->printOptions();


echo "Enabled options: \n";
try {
    $result = $opt->parse( $argv );
    foreach( $result as $key => $spec ) {
        echo $spec . "\n";
    }
} catch( Exception $e ) {
    echo $e->getMessage();
}

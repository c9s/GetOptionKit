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
require 'Universal/ClassLoader/SplClassLoader.php';

require 'tests/helpers.php';
$classLoader = new \Universal\ClassLoader\SplClassLoader( 
    array( "GetOptionKit"  => dirname(__DIR__) . '/src' )
);
$classLoader->register();

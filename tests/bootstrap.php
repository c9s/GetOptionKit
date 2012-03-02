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
require 'vendor/pear/Universal/ClassLoader/SplClassLoader.php';
require 'tests/helpers.php';
$classLoader = new \Universal\ClassLoader\BasePathClassLoader(array(
    dirname(__DIR__) . '/src', 
    dirname(__DIR__) . '/vendor/pear',
));
$classLoader->register();

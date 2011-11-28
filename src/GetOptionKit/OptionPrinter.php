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

class OptionPrinter implements OptionPrinterInterface
{
    public $specs;

    function __construct( GetOptionKit\OptionSpecCollection $specs)
    {
        $this->specs = $specs;
    }

    function print()
    {

    }
}

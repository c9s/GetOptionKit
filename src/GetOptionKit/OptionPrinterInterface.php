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

interface OptionPrinterInterface 
{
    public function __construct(OptionCollection $specs);
    public function printOptions();
}



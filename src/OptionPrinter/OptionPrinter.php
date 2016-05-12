<?php

namespace GetOptionKit\OptionPrinter;

use GetOptionKit\OptionCollection;
use GetOptionKit\Option;

interface OptionPrinter
{
    public function renderOption(Option $option);
    public function render(OptionCollection $options);
}

<?php
namespace GetOptionKit\OptionPrinter;

interface OptionPrinterInterface {
    public function renderOption(Option $option);
    public function render(OptionCollection $options);
}



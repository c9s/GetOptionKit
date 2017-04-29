<?php
use GetOptionKit\OptionPrinter\ConsoleOptionPrinter;
use GetOptionKit\OptionCollection;

class ConsoleOptionPrinterTest extends \PHPUnit\Framework\TestCase
{
    public function test()
    {
        $options = new OptionCollection;
        $options->add('f|foo:', 'option requires a value.' )
            ->isa('String');

        $options->add('b|bar+', 'option with multiple value.' )
            ->isa('Number');

        $options->add('z|zoo?', 'option with optional value.' )
            ->isa('Boolean')
            ;

        $options->add('n', 'n flag' );

        $options->add('verbose', 'verbose');

        $options->add('o|output?', 'option with optional value.' )
            ->isa('File')
            ->defaultValue('output.txt')
            ;
        $printer = new ConsoleOptionPrinter;
        $output = $printer->render($options);
    }

}

GetOptionKit
============

A powerful GetOpt toolkit for PHP, which supports type constraints, flag,
multiple flag, multiple values, required value checking.

GetOptionKit is based on PHP5.3, with fine unit testing with PHPUnit
testing framework.

GetOptionKit is object-oriented, it's flexible and extendable.


## Requirements

* PHP 5.3
* PEAR

## Install

    git clone git://github.com/c9s/GetOptionKit.git
    cd GetOptionKit
    sudo pear install -f package.xml

## Supported formats

flags:

    program.php -a -b -c
    program.php -abc
    program.php -a -bc

with multiple values:

    program.php -a foo -a bar -a zoo -b -b -b

specify value with equal sign:

    program.php -a=foo
    program.php --long=foo

with normal arguments:

    program.php -a=foo -b=bar arg1 arg2 arg3
    program.php arg1 arg2 arg3 -a=foo -b=bar

## Option SPEC

    v|verbose    flag option (with boolean value true)
    d|dir:       option require a value (MUST require)
    d|dir+       option with multiple values.
    d|dir?       option with optional value
    dir:=s       option with type constraint of string
    dir:=string  option with type constraint of string
    dir:=i       option with type constraint of integer
    dir:=integer option with type constraint of integer
    d            single character only option
    dir          long option name



## Demo

Please check `examples/demo.php`.

Run:

    % php examples/demo.php -f test -b 123 -b 333

Print:

    * Available options:
          -f, --foo <value>    option requires a value.
         -b, --bar <value>+    option with multiple value.
        -z, --zoo [<value>]    option with optional value.
              -v, --verbose    verbose message.
                -d, --debug    debug message.
                     --long    long option name only.
                         -s    short option name only.
    Enabled options: 
    * key:foo      spec:-f, --foo <value>  desc:option requires a value.
        value => test

    * key:bar      spec:-b, --bar <value>+  desc:option with multiple value.
        Array
        (
            [0] => 123
            [1] => 333
        )

## Synopsis

    use GetOptionKit\GetOptionKit;

    $getopt = new GetOptionKit;
    $spec = $getopt->add( 'f|foo:' , 'option require value' );  # returns spec object.

    $getopt->add( 'b|bar+' , 'option with multiple value' );
    $getopt->add( 'z|zoo?' , 'option with optional value' );

    $getopt->add( 'f|foo:=i' , 'option require value, with integer type' );
    $getopt->add( 'f|foo:=s' , 'option require value, with string type' );

    $getopt->add( 'v|verbose' , 'verbose flag' );
    $getopt->add( 'd|debug'   , 'debug flag' );

    $result = $opt->parse( array( 'program' , '-f' , 'foo value' , '-v' , '-d' ) );

    $result = $opt->parse( $argv );

    $spec = $result->verbose;
    $spec = $result->debug;
    $spec->value;  # get value

GetOptionKit\OptionPrinter can print options for you:

    * Available options:
                  -f, --foo   option requires a value.
                  -b, --bar   option with multiple value.
                  -z, --zoo   option with optional value.
              -v, --verbose   verbose message.
                -d, --debug   debug message.
                     --long   long option name only.
                         -s   short option name only.

## Todo

* Option Group
* Named Option Value.
* Conflict option checking.
* option valid value checking.

## Command Line Utility Design Concept

* main program name should be easy to type, easy to remember.
* subcommand should be easy to type, easy to remember. length should be shorter than 7 characters.
* options should always have long descriptive name
* a program should be easy to check usage.

## General command interface

To list usage of all subcommands or the program itself:

	$ prog help

To list the subcommand usage

	$ prog help subcommand subcommand2 subcommand3


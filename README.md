GetOptionKit
============

A powerful GetOpt toolkit for PHP, which supports type constraints, flag,
multiple flag, multiple values, required value checking.

GetOptionKit is designed based on PHP5.3, with fine unit testing with PHPUnit
testing framework.

GetOptionKit is object-oriented, it's flexible and extendable.

# Option SPEC

    v|verbose   flag option (with boolean value true)
    d|dir:      option require a value (MUST require)
    d|dir+      option with multiple values.
    d|dir?      option with optional value
    d           single character only option
    dir         long option name

# Supported formats

    program.php -a -b -c
    program.php -abc
    program.php -a -bc

with multiple values

    program.php -a foo -b bar -c zoo

specify value with equal sign:

    program.php -a=foo
    program.php --long=foo

# Synopsis

    use GetOptionKit\GetOptionKit;

    $getopt = new GetOptionKit;
    $getopt->add( 'f|foo:' , 'option require value' );
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

# Command Line Utility Design Concept

* main program name should be easy to type, easy to remember.
* subcommand should be easy to type, easy to remember. length should be shorter than 7 characters.
* options should always have long descriptive name
* a program should be easy to check usage.

# General command interface

To list usage of all subcommands or the program itself:

	$ prog help

To list the subcommand usage

	$ prog help subcommand subcommand2 subcommand3


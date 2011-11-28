CLIFramework SPEC
=================

# option spec

    v|verbose.  flag option
    d|dir:      option require a value
    d|dir+      option with multiple values.
    d|dir?      optional value
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

# Program Interface

    x = new GetOptX;
    x.option  "v|verbose" "verbose message" "key"
    r = x.parse [ argv array .... ]
    r.key




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


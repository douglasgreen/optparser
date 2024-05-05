# optparser
A replacement for getopt. It's better than getopt because:
- It's object-oriented.
- It supports multiple types of commands.
- It checks for unrecognized arguments.
- It returns arguments as a specific scalar type.

## Description

A program accepts one or more types of commands. A command is a series of
options, each of which can be required or not required.

An option is either:
- A flag, with zero arguments.
- A parameter, with one required argument.

## References
* http://docopt.org/
* https://github.com/nategood/commando
* https://github.com/getopt-php/getopt-php
* https://www.php.net/manual/en/function.getopt.php

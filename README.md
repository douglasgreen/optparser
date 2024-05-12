# optparser

A replacement for getopt. It's better than getopt because:

-   It's object-oriented.
-   It supports multiple types of commands.
-   It checks for unrecognized arguments.
-   It returns arguments as a specific scalar type.

## Commands

A program accepts one or more types of usage. A usage is a series of options,
each of which can be required or extra (not required).

## Options

An option is one of the following:

-   A command, which is the name of a requested operation.
-   A term, which is a positional argument.
-   A flag, with zero arguments.
-   A parameter, with one required argument.

Options are processed in the order given.

If there is a command, there can only be one command and it must come first.

If there are terms, they must follow the command, if any.

Flags and parameters come last.

The order is important to avoid ambiguity.

## Command matching

To find a match, each command is tried in order. The first command that
completely matches with no leftover arguments is used. If no match is found, an
error code is returned and the help message is printed.

## Argument aliases

Each argument can have one or more aliases. If an alias is short (only one
character), it is invoked with a single hyphen like `-h`. If an alias is long
(more than one character), it is invoked with two hyphens like `--help`.

Each alias must be defined only once.

## Argument names

Every argument must have at least one long alias. The first long alias that is
specified for that argument is used as the argument name. You must retrieve the
argument using its name.

A command can have aliases. But a term is not marked so it only has a name, not
aliases.

## Formatting

Flags can be combined, for example -a -b -c can be written as -abc. However a
combined flag can't take arguments.

A space or equal sign must separate every flag or parameter from its argument.

The argument list can be terminated by --, which can be followed by non-options.

## References

-   http://docopt.org/
-   https://github.com/nategood/commando
-   https://github.com/getopt-php/getopt-php
-   https://www.php.net/manual/en/function.getopt.php
-   https://www.gnu.org/software/libc/manual/html_node/Argument-Syntax.html

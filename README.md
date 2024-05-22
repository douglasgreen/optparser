# optparser

A replacement for getopt. It's better than getopt because:

-   It's object-oriented.
-   It supports multiple types of commands.
-   It checks for unrecognized arguments.
-   It returns arguments as a specific scalar type.

## Commands

A program accepts one or more types of usage. A usage is a series of options.

## Options

An option is one of the following:

-   A command, which is the name of a requested operation.
-   A term, which is a positional argument.
-   A flag, with zero arguments.
-   A parameter, with one required argument.

Options are processed in the order given.

If there is a command, there can only be one command and it must come first. And
every usage must have a command to distinguish them.

If there are terms, they must follow the command, if any.

Flags and parameters come last. The order is important to avoid ambiguity.

Commands and terms are required but flags and parameters are optional.

## Naming convention

Commands, flags, and parameters must all follow the Unix convention:

-   All lowercase
-   Start with a letter
-   Contain letters, digits, and hyphens but no underscores
-   End in a letter or digit

That is also known as kebab case.

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

Commands, parameters, and flags can have aliases. But a term is not marked so it
only has a name, not aliases.

## Formatting

Flags can be combined, for example -a -b -c can be written as -abc. However a
combined flag can't take arguments.

A space or equal sign must separate every flag or parameter from its argument.

The argument list can be terminated by --, which can be followed by non-options.
The program ignores non-options but returns them with the matched usage.

## Fetching results

Results are returned by OptParser::matchUsage() as an OptResult object. Options
are returned as attributes of the object. Camel case in attribute names is
mapped to kebab case in option names. For example, `$result->filePath` would map
to the `file-path` option.

## Developer setup

See [Setup Guide](docs/setup_guide.md) for steps to set up for development.

## References

-   http://docopt.org/
-   https://github.com/nategood/commando
-   https://github.com/getopt-php/getopt-php
-   https://www.php.net/manual/en/function.getopt.php
-   https://www.gnu.org/software/libc/manual/html_node/Argument-Syntax.html

# optparser

A replacement for getopt. It's better than getopt because:

-   It supports multiple command usages.
-   It automatically prints formatted program help.
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

If there is a command, there can only be one command and it must come first. If
there are more than one usage, each usage must have a command to distinguish it.

If there are terms, they are required and must follow the command, if any.

Flags and parameters come last. The order is important to avoid ambiguity.

Commands and terms are required, because they are positional, but flags and
parameters are optional, because they are named.

## Naming convention

Commands, flags, and parameters must all follow the Unix convention for their
names:

-   All lowercase
-   Start with a letter
-   Contain letters, digits, and hyphens but no underscores
-   End in a letter or digit

That is also known as kebab case. An example name is `file-path`.

## Command matching

Each usage is distinguished by its command. Each command is different and so
only one usage can possibly match at runtime.

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

## Argument types

The list of permitted argument types is taken from the list of
[PHP validation filters](https://www.php.net/manual/en/filter.filters.validate.php).
Permitted types include:

-   `bool`
-   `domain`
-   `email`
-   `float`
-   `int`
-   `ip_addr`
-   `mac_addr`
-   `string`
-   `url`

These are specified as the second argument of `OptParser::addParam()` and
`OptParser::addTerm()`, because parameters and terms accept arguments and
therefore have type. These types are printed in program help and applied
automatically during argument processing, resulting in program error and
termination on failure to validate.

## Argument filters

You can define your own filter callback as the last argument of
`OptParser::addParam()` and `OptParser::addTerm()` also. The filter can do
custom validation. If validation succeeds, you can return the original value or
a filtered version of it. If validation fails, return null, and the program will
error and terminate.

## Formatting

Flags can be combined, for example -a -b -c can be written as -abc. However a
combined flag can't take arguments.

A space or equal sign must separate every flag or parameter from its argument.

The argument list can be terminated by --, which can be followed by non-options.
The program ignores non-options but returns them with the matched usage.

## Fetching results

Results are returned by `OptParser::matchUsage()` as an `OptResult` object which
I call `$input` as it represents user input.

You can retrieve matched arguments from the user with `$input->get($name)`,
where name was the option name. You can also retrieve arguments with as
attributes with `$input->$name`. Camel case in the attribute names is mapped to
kebab case in option names. For example, `$result->filePath` would map to the
`file-path` option name.

## Developer setup

See [Setup Guide](docs/setup_guide.md) for steps to set up for development.

## Sample usage

There is a [sample usage](bin/sample_usage.php) in a file. This usage shows an
example of adding three separate commands. The terms, flags, and params used by
the command are defined next. Then the usages that combine relevant commands and
options are defined next.

After matching the right usage, the inputs are dumped using attribute syntax.
They could also be dumped using `$input->get()`.

## References

-   http://docopt.org/
-   https://github.com/nategood/commando
-   https://github.com/getopt-php/getopt-php
-   https://www.php.net/manual/en/function.getopt.php
-   https://www.gnu.org/software/libc/manual/html_node/Argument-Syntax.html

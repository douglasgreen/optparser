<?php

declare(strict_types=1);

namespace DouglasGreen\OptParser;

/**
 * Parse command-line arguments from $args.
 *
 * This class parses command-line arguments according to the GNU argument syntax.
 *
 * @see https://www.gnu.org/software/libc/manual/html_node/Argument-Syntax.html
 */
class ArgParser
{
    /**
     * @var list<string> Non-option arguments
     */
    protected $nonOptions = [];

    /**
     * @var array<string, string> Marked options (options with leading dash)
     */
    protected $markedOptions = [];

    /**
     * @var string Program name
     */
    protected string $programName;

    /**
     * @var list<string> Unmarked options (options without leading dash)
     */
    protected $unmarkedOptions = [];

    /**
     * Constructor.
     *
     * @param list<string> $args Command-line arguments
     *
     * @throws ValidationException If no program name is provided
     */
    public function __construct(array $args)
    {
        $programName = array_shift($args);
        if (! $programName) {
            throw new ValidationException('No program name');
        }

        $this->programName = basename($programName);
        [$options, $this->nonOptions] = $this->splitArrayAroundDash($args);
        $options = $this->joinArguments($options);
        foreach ($options as $option) {
            if (preg_match('/^--?(\w+(-\w+)*)(=(.*))?/', $option, $match)) {
                $name = $match[1];
                $arg = $match[4] ?? '';
                $this->markedOptions[$name] = $arg;
            } else {
                $this->unmarkedOptions[] = $option;
            }
        }
    }

    /**
     * Get the marked options.
     *
     * @return array<string, string> Marked options as key-value pairs
     */
    public function getMarkedOptions(): array
    {
        return $this->markedOptions;
    }

    /**
     * Get the non-option arguments.
     *
     * @return list<string> Non-option arguments
     */
    public function getNonOptions(): array
    {
        return $this->nonOptions;
    }

    /**
     * Get the program name.
     *
     * @return string Program name
     */
    public function getProgramName(): string
    {
        return $this->programName;
    }

    /**
     * Get the unmarked options.
     *
     * @return list<string> Unmarked options
     */
    public function getUnmarkedOptions(): array
    {
        return $this->unmarkedOptions;
    }

    /**
     * Split an array into two parts around the '--' separator.
     *
     * @param list<string> $array Input array
     *
     * @return array{list<string>, list<string>} Array with elements before '--' and after '--'
     */
    protected function splitArrayAroundDash(array $array): array
    {
        // Find the index of '--'
        $dashIndex = array_search('--', $array, true);

        // Check if '--' was found
        if ($dashIndex === false) {
            // '--' is not in the array, return the whole array and an empty array
            return [$array, []];
        }

        // Split the array into two subarrays
        $before = array_slice($array, 0, $dashIndex);
        $after = array_slice($array, $dashIndex + 1);

        return [$before, $after];
    }

    /**
     * Join option names with their arguments using '='.
     *
     * @param list<string> $array Input array of options and arguments
     *
     * @return list<string> Array with joined options and arguments
     */
    protected function joinArguments(array $array): array
    {
        $newArray = [];
        $index = 0;
        $length = count($array);
        while ($index < $length) {
            $value = $array[$index];
            if (preg_match('/^-(\w{2,})/', $value, $match)) {
                // Split up -abc into -a -b -c.
                $chars = str_split($match[1]);
                foreach ($chars as $char) {
                    $newArray[] = '-' . $char;
                }
            } elseif (
                preg_match('/^(-\w|--\w+(-\w+)*)\b/', $value)
                && isset($array[$index + 1])
                && preg_match('/^-/', $array[$index + 1]) === 0
            ) {
                // Join parameter with argument by =.
                $value .= '=' . $array[++$index];
                $newArray[] = $value;
            } else {
                $newArray[] = $value;
            }

            ++$index;
        }

        return $newArray;
    }
}

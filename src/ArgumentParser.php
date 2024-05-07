<?php

declare(strict_types=1);

namespace DouglasGreen\OptParser;

/**
 * Parse $argv.
 *
 * @see https://www.gnu.org/software/libc/manual/html_node/Argument-Syntax.html
 */
class ArgumentParser
{
    /**
     * @var list<string>
     */
    protected $nonOptions = [];

    protected string $programName;

    /**
     * @var list<string>
     */
    protected $unmarkedOptions = [];

    /**
     * @var array<string, string>
     */
    protected $markedOptions = [];

    /**
     * @param list<string> $argv
     */
    public function __construct(array $argv)
    {
        $programName = array_shift($argv);
        if (! $programName) {
            throw new OptParserException('No program name');
        }

        $this->programName = basename($programName);
        [$options, $this->nonOptions] = $this->splitArrayAroundDash($argv);
        $options = $this->joinArguments($options);
        foreach ($options as $option) {
            if (preg_match('/^--?(\w+)(=(.*))?/', $option, $match)) {
                $name = $match[1];
                $arg = $match[3] ?? '';
                $this->markedOptions[$name] = $arg;
            } else {
                $this->unmarkedOptions[] = $option;
            }
        }
    }

    /**
     * @return array<string, string>
     */
    public function getMarkedOptions(): array
    {
        return $this->markedOptions;
    }

    /**
     * @return list<string>
     */
    public function getNonOptions(): array
    {
        return $this->nonOptions;
    }

    public function getProgramName(): string
    {
        return $this->programName;
    }

    /**
     * @return list<string>
     */
    public function getUnmarkedOptions(): array
    {
        return $this->unmarkedOptions;
    }

    /**
     * @param list<string> $array
     *
     * @return array{list<string>, list<string>}
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
     * @param list<string> $array
     *
     * @return list<string>
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
                preg_match('/^(-\w|--\w+)\b/', $value)
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

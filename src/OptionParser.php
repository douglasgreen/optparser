<?php

namespace DouglasGreen\OptionParser;

class OptionParser
{
    /** @var array */
    protected $args = [];

    public function __construct(array $args)
    {
        foreach ($args as $arg) {
            // Add space between arguments.
            if ($i > 0) {
                $this->input .= ' ';
            }

            // Add space between single-char flag and argument.
            if (preg_match('/^-(\w)(.*)/', $arg, $match)) {
                $arg = '-' . $match[1] . ' ' . $match[2];
            }
            $this - input .= $arg;
        }
    }

    public function getString(string $name, string $letter = null, bool $required = false): ?string
    {
    }
}

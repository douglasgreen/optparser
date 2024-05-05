<?php

namespace DouglasGreen\OptParser;

use DouglasGreen\OptParser\Option\Command;
use DouglasGreen\OptParser\Option\Flag;
use DouglasGreen\OptParser\Option\Option;
use DouglasGreen\OptParser\Option\Param;
use DouglasGreen\OptParser\Option\Term;

/**
 * Define and print options.
 */
class OptionHandler
{
    /** @var array<string, bool> */
    protected $allAliases = [];

    /**
     * @var array<string, Command>
     */
    protected $commands = [];

    /**
     * @var array<string, Flag>
     */
    protected $flags = [];

    /**
     * @var array<string, Param>
     */
    protected $params = [];

    /**
     * @var array<string, Term>
     */
    protected $terms = [];

    public function __construct()
    {
        $this->addFlag(['h', 'help'], 'Display program help');
    }

    /**
     * A command is a predefined list of command words.
     *
     * @param list<string> $aliases
     */
    public function addCommand(array $aliases, string $desc): void
    {
        [$name, $others] = $this->pickName($aliases);
        $this->commands[$name] = new Command($name, $desc, $others);
    }

    /**
     * A flag has no arguments.
     *
     * @param list<string> $aliases
     */
    public function addFlag(array $aliases, string $desc): void
    {
        [$name, $others] = $this->pickName($aliases);
        $this->flags[$name] = new Flag($name, $desc, $others);
    }

    /**
     * A parameter has a required argument.
     *
     * @param list<string> $aliases
     */
    public function addParam(array $aliases, string $type, string $desc): void
    {
        [$name, $others] = $this->pickName($aliases);
        $this->params[$name] = new Param($name, $desc, $others, $type);
    }

    /**
     * A term is a positional argument.
     */
    public function addTerm(string $name, string $type, string $desc): void
    {
        $this->checkAlias($name);
        $this->terms[$name] = new Term($name, $desc, $type);
    }

    /**
     * Get the type of a command.
     */
    public function getType(string $name): ?string
    {
        $option = $this->getOption($name);
        return $option->getType();
    }

    /*
     * Get an option by name.
     *
     * @throws OptParserException
     */
    public function getOption(string $name): Option
    {
        if (isset($this->commands[$name])) {
            return $this->commands[$name];
        }

        if (isset($this->params[$name])) {
            return $this->params[$name];
        }

        if (isset($this->terms[$name])) {
            return $this->terms[$name];
        }

        if (isset($this->flags[$name])) {
            return $this->flags[$name];
        }

        throw new OptParserException("Name not found");
    }

    /**
     * Match a command.
     */
    public function matchCommand(string $name, string $value): bool
    {
        if (!isset($this->commands[$name])) {
            return false;
        }

        $command = $this->commands[$name];
        return $command->matchInput($value);
    }

    /**
     * Match a term.
     */
    public function matchTerm(string $name, string $value): bool
    {
        if (!isset($this->terms[$name])) {
            return false;
        }

        $term = $this->terms[$name];
        return $term->matchInput($value);
    }

    /**
     * Write an option by name.
     */
    public function writeOption(string $name): string
    {
        $option = $this->getOption($name);
        return $option->write();
    }

    /**
     * Get a string representation of options, arguments, and descriptions.
     */
    public function writeOptionBlock(): string
    {
        $output = '';

        if ($this->commands) {
            $output .= $this->writeCommandBlock();
        }

        if ($this->params) {
            $output .= $this->writeParamBlock();
        }

        if ($this->flags) {
            $output .= $this->writeFlagBlock();
        }

        if ($this->terms) {
            $output .= $this->writeTermBlock();
        }

        return $output;
    }

    /**
     * Check alias for uniqueness.
     *
     * @throws OptParserException
     */
    protected function checkAlias(string $alias): void
    {
        if (isset($this->allAliases[$alias])) {
            throw new OptParserException("Duplicate alias: " . $alias);
        }
        $this->allAliases[$alias] = true;
    }

    /**
     * @param list<string> $aliases
     * @return array{string, list<string>}
     * @throws OptParserException
     */
    protected function pickName(array $aliases): array
    {
        $name = null;
        $others = [];
        foreach ($aliases as $alias) {
            $this->checkAlias($alias);
            if (!$name && strlen($alias) > 1) {
                $name = $alias;
            } else {
                $others[] = $alias;
            }
        }

        if (!$name) {
            throw new OptParserException("Missing required long name");
        }

        return [$name, $others];
    }

    protected function writeCommandBlock(): string
    {
        $output = "Commands:\n";
        foreach ($this->commands as $name => $command) {
            $output .= "  $name";
            $aliases = $command->getAliases();
            if ($aliases) {
                foreach ($aliases as $alias) {
                    $output .= " | $alias";
                }
            }
            $output .= '  ' . $command->getDesc() . "\n";
        }
        $output .= "\n";
        return $output;
    }

    protected function writeFlagBlock(): string
    {
        $output = "Flags:\n";
        foreach ($this->flags as $name => $flag) {
            $output .= '  ';
            $output .= $flag->hyphenate($name);
            $aliases = $flag->getAliases();
            if ($aliases) {
                foreach ($aliases as $alias) {
                    $output .= ' | ';
                    $output .= $flag->hyphenate($alias);
                }
            }
            $output .= '  ' . $flag->getDesc() . "\n";
        }
        $output .= "\n";
        return $output;
    }

    protected function writeParamBlock(): string
    {
        $output = "Parameters:\n";
        foreach ($this->params as $name => $param) {
            $output .= '  ';
            $output .= $param->hyphenate($name);
            $aliases = $param->getAliases();
            if ($aliases) {
                foreach ($aliases as $alias) {
                    $output .= ' | ';
                    $output .= $param->hyphenate($alias);
                }
            }
            $output .= ' = ' . $param->getType();
            $output .= '  ' . $param->getDesc() . "\n";
        }
        $output .= "\n";
        return $output;
    }

    protected function writeTermBlock(): string
    {
        $output = "Terms:\n";
        foreach ($this->terms as $name => $term) {
            $output .= "  $name: " . $term->getType() . '  ' . $term->getDesc() . "\n";
        }
        $output .= "\n";
        return $output;
    }
}

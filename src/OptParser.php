<?php

declare(strict_types=1);

namespace DouglasGreen\OptParser;

/**
 * Define a program with a series of usage options.
 */
class OptParser
{
    /**
     * @var ArgParser
     */
    public $argParser;

    /**
     * @var OptHandler
     */
    public $optHandler;

    /**
     * @var list<Usage>
     */
    protected $usages = [];

    /**
     * @param string[] $argv
     */
    public function __construct(
        protected array $argv,
        protected string $name,
        protected string $desc
    ) {
        $this->optHandler = new OptHandler();
        $this->argParser = new ArgParser($argv);

        // Add a default help usage.
        $this->addUsage(['help']);
    }

    /**
     * A command is a predefined list of command words.
     *
     * @param list<string> $aliases
     */
    public function addCommand(array $aliases, string $desc): self
    {
        $this->optHandler->addCommand($aliases, $desc);

        return $this;
    }

    /**
     * A flag has no arguments.
     *
     * @param list<string> $aliases
     */
    public function addFlag(array $aliases, string $desc): self
    {
        $this->optHandler->addFlag($aliases, $desc);

        return $this;
    }

    /**
     * A parameter has a required argument.
     *
     * @param list<string> $aliases
     */
    public function addParam(array $aliases, string $type, string $desc): self
    {
        $this->optHandler->addParam($aliases, $type, $desc);

        return $this;
    }

    /**
     * A term is a positional argument.
     */
    public function addTerm(string $name, string $type, string $desc): self
    {
        $this->optHandler->addTerm($name, $type, $desc);

        return $this;
    }

    /**
     * Add a usage to the command by name.
     *
     * @param list<string> $optionNames
     */
    public function addUsage(array $optionNames): self
    {
        $this->usages[] = new Usage($this->optHandler, $optionNames);

        return $this;
    }

    public function addUsageAll(): self
    {
        $allNames = $this->optHandler->getAllNames();
        $this->addUsage($allNames);

        return $this;
    }

    /**
     * @return ?Usage
     * @todo Make usage store values?
     */
    public function matchUsage(): ?Usage
    {
        foreach ($this->usages as $usage) {
            $match = $this->tryToMatchUsage($usage);
            if ($match !== null) {
                return $match;
            }
        }

        return null;
    }

    /**
     * Write the options line for the command.
     */
    public function writeHelp(): string
    {
        $output = $this->name . "\n\n";
        $output .= wordwrap($this->desc) . "\n\n";
        $output .= "Usage:\n";
        $programName = $this->argParser->getProgramName();
        foreach ($this->usages as $usage) {
            $output .= $usage->write($programName);
        }

        $output .= "\n";

        return $output . $this->optHandler->writeOptionBlock();
    }

    /**
     * Match the usage and return an array whose keys are the name of the
     * options being match and whose values are:
     * - true|false for commands
     * - value for terms
     * - true|false for flags
     * - value for params.
     */
    protected function tryToMatchUsage(Usage $usage): ?array
    {
        $unmarkedOptions = $this->argParser->getUnmarkedOptions();
        $this->argParser->getMarkedOptions();
        $this->argParser->getNonOptions();

        $commands = $usage->getOptions('command');
        if ($this->matchCommands($commands, $unmarkedOptions)) {
        }

        $usage->getOptions('term');
    }

    /*
    protected function tryToMatchUsage(Usage $usage): ?string
    {
        $unmarkedOptions = $this->argParser->getUnmarkedOptions();
        // $markedOptions = $this->argParser->getMarkedOptions();
        // $nonOptions = $this->argParser->getNonOptions();
        $matches = [];

        $commands = $usage->getOptions('command');
        foreach ($commands as $name => $required) {
            $command = $this->optHandler->getOption($name);
            $found = false;
            $matches[$name] = false;
            if ($unmarkedOptions !== []) {
                $value = array_shift($unmarkedOptions);
                $isMatch = $command->matchInput($value);
                if ($isMatch) {
                    $matches[$name] = true;
                    $found = true;
                } else {
                    array_unshift($unmarkedOptions, $value);
                    $found = false;
                }
            }

            if (! $required) {
                continue;
            }

            if ($found) {
                continue;
            }

            return null;
        }

        $terms = $usage->getOptions('term');
        foreach ($terms as $name => $required) {
            $term = $this->optHandler->getOption($name);
            $found = false;
            $matches[$name] = false;
            if ($unmarkedOptions !== []) {
                $value = array_shift($unmarkedOptions);
                $isMatch = $term->matchInput($value);
                if ($isMatch) {
                    $matches[$name] = true;
                    $found = true;
                } else {
                    array_unshift($unmarkedOptions, $value);
                    $found = false;
                }
            }

            if (! $required) {
                continue;
            }

            if ($found) {
                continue;
            }

            return null;
        }

        return '';
    }
    */
}

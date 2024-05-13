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
     *
     * @throws OptParserException
     */
    public function addCommand(array $aliases, string $desc): self
    {
        if (count($this->usages) > 1) {
            throw new OptParserException('Cannot add commands after usages');
        }

        $this->optHandler->addCommand($aliases, $desc);

        return $this;
    }

    /**
     * A flag has no arguments.
     *
     * @param list<string> $aliases
     *
     * @throws OptParserException
     */
    public function addFlag(array $aliases, string $desc): self
    {
        if (count($this->usages) > 1) {
            throw new OptParserException('Cannot add flags after usages');
        }

        $this->optHandler->addFlag($aliases, $desc);

        return $this;
    }

    /**
     * A parameter has a required argument.
     *
     * @param list<string> $aliases
     *
     * @throws OptParserException
     */
    public function addParam(array $aliases, string $type, string $desc): self
    {
        if (count($this->usages) > 1) {
            throw new OptParserException('Cannot add params after usages');
        }

        $this->optHandler->addParam($aliases, $type, $desc);

        return $this;
    }

    /**
     * A term is a positional argument.
     *
     * @throws OptParserException
     */
    public function addTerm(string $name, string $type, string $desc): self
    {
        if (count($this->usages) > 1) {
            throw new OptParserException('Cannot add terms after usages');
        }

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
        // If a command is defined, all usages must have a command.
        if ($this->optHandler->hasOptionType('command')) {
            $hasCommand = false;
            foreach ($optionNames as $optionName) {
                if ($this->optHandler->getOptionType($optionName) === 'command') {
                    $hasCommand = true;
                    break;
                }
            }

            if (! $hasCommand) {
                throw new OptParserException('Must define command for each usage');
            }
        } elseif (count($this->usages) > 2) {
            // Multiple usages besides help must define a command.
            throw new OptParserException('Must define command for each usage');
        }

        $this->usages[] = new Usage($this->optHandler, $optionNames);

        return $this;
    }

    /**
     * Add all options to a single usage except "help".
     */
    public function addUsageAll(): self
    {
        $optionNames = $this->optHandler->getAllNames();
        $filteredOptions = array_filter($optionNames, function($option) {
            return $option !== "help";
        });

        $this->addUsage($filteredOptions);

        return $this;
    }

    /**
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
        $markedOptions = $this->argParser->getMarkedOptions();
        $nonOptions = $this->argParser->getNonOptions();

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

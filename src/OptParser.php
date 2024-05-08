<?php

declare(strict_types=1);

namespace DouglasGreen\OptParser;

/**
 * Define a program with a series of usage options.
 */
class OptParser
{
    /**
     * @var ArgumentParser
     */
    public $argumentParser;

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
        $this->argumentParser = new ArgumentParser($argv);

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
     * @param list<string> $requiredOptions
     * @param list<string> $extraOptions
     */
    public function addUsage(array $requiredOptions, array $extraOptions = []): self
    {
        $this->usages[] = new Usage($this->optHandler, $requiredOptions, $extraOptions);

        return $this;
    }

    public function matchUsage(): ?Usage
    {
        foreach ($this->usages as $usage) {
            $matches = $this->tryToMatchUsage($usage);
            if ($matches === null) {
                continue;
            }

            if ($matches === '') {
                continue;
            }

            if ($matches === '0') {
                continue;
            }

            return $usage;
        }

        return null;
    }

    /**
     * Write the options line for the command.
     */
    public function writeHelpBlock(): string
    {
        $output = $this->name . "\n\n";
        $output .= wordwrap($this->desc) . "\n\n";
        $output .= "Usage:\n";
        $programName = $this->argumentParser->getProgramName();
        foreach ($this->usages as $usage) {
            $output .= $usage->writeOptionsLine($programName);
        }

        $output .= "\n";

        return $output . $this->optHandler->writeOptionBlock();
    }

    /**
     * @todo Finish
     */
    protected function tryToMatchUsage(Usage $usage): ?string
    {
        $unmarkedOptions = $this->argumentParser->getUnmarkedOptions();
        // $markedOptions = $this->argumentParser->getMarkedOptions();
        // $nonOptions = $this->argumentParser->getNonOptions();
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
}

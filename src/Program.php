<?php

declare(strict_types=1);

namespace DouglasGreen\OptParser;

/**
 * Define a program with a series of usage options.
 */
class Program
{
    /**
     * @var list<Usage>
     */
    protected $usages = [];

    public function __construct(
        protected ArgumentParser $argumentParser,
        protected OptionHandler $optionHandler,
        protected string $name,
        protected string $desc
    ) {
        // Add a default help usage.
        $usage = new Usage($this->optionHandler);
        $usage->addOption('help', true);
        $this->addUsage($usage);
    }

    /**
     * Add a usage to the command by name.
     */
    public function addUsage(Usage $usage): void
    {
        $this->usages[] = $usage;
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

        return $output . $this->optionHandler->writeOptionBlock();
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
            $command = $this->optionHandler->getOption($name);
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
            $term = $this->optionHandler->getOption($name);
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

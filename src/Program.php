<?php

namespace DouglasGreen\OptParser;

/**
 * Define a program with a series of usage options.
 */
class Program
{
    /** @var ArgumentParser */
    protected $argumentParser;

    /** @var string */
    protected $desc;

    /** @var string */
    protected $name;

    /** @var OptionHandler */
    protected $optionHandler;

    /** @var list<Usage> */
    protected $usages = [];

    public function __construct(
        ArgumentParser $argumentParser,
        OptionHandler $optionHandler,
        string $name,
        string $desc
    ) {
        $this->argumentParser = $argumentParser;
        $this->optionHandler = $optionHandler;
        $this->name = $name;
        $this->desc = $desc;

        // Add a default help usage.
        $usage = new Usage($optionHandler);
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
            if ($matches) {
                return $usage;
            }
        }
        return null;
    }

    /**
     * Write the options line for the command.
     */
    public function writeHelpBlock(): string
    {
        $output = $this->name . "\n\n";
        $output = wordwrap($this->desc) . "\n\n";
        $output .= "Usage:\n";
        $programName = $this->argumentParser->getProgramName();
        foreach ($this->usages as $usage) {
            $output .= $usage->writeOptionsLine($programName);
        }
        $output .= "\n";
        $output .= $this->optionHandler->writeOptionBlock();
        return $output;
    }

    protected function tryToMatchUsage(Usage $usage): ?array
    {
        $unmarkedOptions = $this->argumentParser->getUnmarkedOptions();
        //$markedOptions = $this->argumentParser->getMarkedOptions();
        //$nonOptions = $this->argumentParser->getNonOptions();
        $matches = [];

        $commands = $usage->getOptions('command');
        if ($commands) {
            foreach ($commands as $name => $required) {
                $command = $this->optionHandler->getOption($name);
                $found = false;
                $matches[$name] = false;
                if ($unmarkedOptions) {
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

                if ($required && !$found) {
                    return null;
                }
            }
        }

        $terms = $usage->getOptions('term');
        if ($terms) {
            foreach ($terms as $name => $required) {
                $term = $this->optionHandler->getOption($name);
                $found = false;
                $matches[$name] = false;
                if ($unmarkedOptions) {
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

                if ($required && !$found) {
                    return null;
                }
            }
        }
    }
}

<?php

namespace DouglasGreen\OptParser;

/**
 * Define a command with a series of options.
 */
class CommandHandler
{
    /** @var OptionHandler */
    protected $optionHandler;

    /** @var array<string, array<string, bool>> */
    protected $options = [
        'command' => [],
        'term' => [],
        'flag' => [],
        'param' => []
    ];

    public function __construct(OptionHandler $optionHandler)
    {
        $this->optionHandler = $optionHandler;
    }

    /**
     * Add an option to the command by name.
     *
     * @throws OptionParserException
     */
    public function addOption(string $name, bool $required = false): void
    {
        $type = $this->optionHandler->getType($name);
        $this->options[$type][$name] = $required;
    }

    /**
     * Write the options line for the command.
     */
    public function writeOptionsLine(string $programName): string
    {
        $output = '  ' . $programName;
        foreach ($this->options as $names) {
            foreach ($names as $name => $required) {
                $output .= ' ';
                if (!$required) {
                    $output .= "[";
                }
                $output .= $this->optionHandler->writeOption($name);
                if (!$required) {
                    $output .= "[";
                }
            }
        }
        $output .= "\n";
        return $output;
    }
}

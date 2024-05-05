<?php

namespace DouglasGreen\OptParser;

/**
 * Define a usage with a series of options.
 */
class Usage
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
     * Add an option to the usage by name.
     *
     * @throws OptParserException
     */
    public function addOption(string $name, bool $required = false): void
    {
        $type = $this->optionHandler->getType($name);
        if ($type == 'command' && $this->options['command']) {
            throw new OptParserException('Multiple commands defined');
        }

        $this->options[$type][$name] = $required;
    }

    /**
     * @return array<string, bool>
     *
     * @throws OptParserException
     */
    public function getOptions(string $type): array
    {
        if (isset($this->options[$type])) {
            return $this->options[$type];
        }

        throw new OptParserException('Invalid type');
    }

    /**
     * Write the options line for the usage.
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
<?php

declare(strict_types=1);

namespace DouglasGreen\OptParser;

/**
 * Define a usage with a series of options.
 */
class Usage
{
    /**
     * @var array<string, array<string, bool>>
     */
    protected $options = [
        'command' => [],
        'term' => [],
        'flag' => [],
        'param' => [],
    ];

    public function __construct(
        protected OptionHandler $optionHandler
    ) {
    }

    /**
     * Add an option to the usage by name.
     */
    public function addOption(string $name, bool $required = false): void
    {
        $type = $this->optionHandler->getType($name);
        if ($type === 'command' && $this->options['command']) {
            throw new OptParserException('Multiple commands defined');
        }

        $this->options[$type][$name] = $required;
    }

    /**
     * @return array<string, bool>
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
        foreach ($this->options as $option) {
            foreach ($option as $name => $required) {
                $output .= ' ';
                if (! $required) {
                    $output .= '[';
                }

                $output .= $this->optionHandler->writeOption($name);
                if (! $required) {
                    $output .= ']';
                }
            }
        }

        return $output . "\n";
    }
}

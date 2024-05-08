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
        'param' => [],
        'flag' => [],
    ];

    /**
     * Constructor for the Usage class.
     *
     * @param OptHandler    $optHandler      The option handler instance
     * @param array<string> $requiredOptions An array of required option names
     * @param array<string> $extraOptions    An array of extra option names
     *
     * @throws OptParserException If multiple commands are defined
     */
    public function __construct(
        protected OptHandler $optHandler,
        array $requiredOptions,
        array $extraOptions
    ) {
        foreach ($requiredOptions as $requiredOption) {
            $type = $this->optHandler->getType($requiredOption);
            if ($type === 'command' && $this->options['command']) {
                throw new OptParserException('Multiple commands defined');
            }

            $this->options[$type][$requiredOption] = true;
        }

        foreach ($extraOptions as $extraOption) {
            $type = $this->optHandler->getType($extraOption);
            $this->options[$type][$extraOption] = false;
        }
    }

    /**
     * Get the options of a specific type.
     *
     * @param string $type The type of options to retrieve
     *
     * @return array<string, bool> The options of the specified type
     *
     * @throws OptParserException If an invalid type is provided
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
     *
     * @param string $programName The name of the program
     *
     * @return string The options line for the usage
     */
    public function writeOptionsLine(string $programName): string
    {
        $output = '  ' . $programName;
        foreach ($this->options as $option) {
            foreach ($option as $name => $required) {
                if ($required) {
                    $output .= ' ' . $this->optHandler->writeOption($name);
                } else {
                    $output .= ' [' . $this->optHandler->writeOption($name) . ']';
                }
            }
        }

        return $output . "\n";
    }
}

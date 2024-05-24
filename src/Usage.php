<?php

declare(strict_types=1);

namespace DouglasGreen\OptParser;

/**
 * Define a usage with a series of options.
 */
class Usage
{
    /**
     * @var array<string, list<string>>
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
     * @param OptHandler    $optHandler  The option handler instance
     * @param array<string> $optionNames Options of this usage
     *
     * @throws ValidationException If multiple commands are defined
     */
    public function __construct(
        protected OptHandler $optHandler,
        array $optionNames
    ) {
        foreach ($optionNames as $optionName) {
            $optionType = $this->optHandler->getOptionType($optionName);
            if ($optionType === 'command' && $this->options['command']) {
                throw new ValidationException('Multiple commands defined');
            }

            // Eliminate dupes.
            if (in_array($optionName, $this->options[$optionType], true)) {
                continue;
            }

            $this->options[$optionType][] = $optionName;
        }
    }

    /**
     * Get the options of a specific type.
     *
     * @param string $optionType The type of options to retrieve
     *
     * @return list<string> The options of the specified type
     *
     * @throws ValidationException If an invalid type is provided
     */
    public function getOptions(string $optionType): array
    {
        if (isset($this->options[$optionType])) {
            return $this->options[$optionType];
        }

        throw new ValidationException('Invalid type: ' . $optionType);
    }

    /**
     * Write the options line for the usage.
     *
     * @param string $programName The name of the program
     *
     * @return string The options line for the usage
     */
    public function write(string $programName): string
    {
        $output = '  ' . $programName;
        foreach ($this->options as $option) {
            foreach ($option as $optionName) {
                $output .= ' ' . $this->optHandler->writeOption($optionName);
            }
        }

        return $output . "\n";
    }
}

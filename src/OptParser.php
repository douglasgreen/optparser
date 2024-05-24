<?php

declare(strict_types=1);

namespace DouglasGreen\OptParser;

/**
 * Define a program with a series of usage options.
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
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
    public $usages = [];

    /**
     * @var bool All non-help usages have commands. If allCommands is false,
     * that means there are no commands because a program with only one usage
     * that has a command would also be allCommands = true.
     */
    protected $allCommands = true;

    public function __construct(
        protected string $name,
        protected string $desc,
        protected bool $debugMode = false
    ) {
        $this->optHandler = new OptHandler();

        // Add a default help usage.
        $this->usages[] = new Usage($this->optHandler, ['help']);
    }

    /**
     * A command is a predefined list of command words.
     *
     * @param list<string> $aliases
     *
     * @throws ValidationException
     */
    public function addCommand(array $aliases, string $desc): self
    {
        if (count($this->usages) > 1) {
            throw new ValidationException('Cannot add commands after usages');
        }

        $this->optHandler->addCommand($aliases, $desc);

        return $this;
    }

    /**
     * A flag has no arguments.
     *
     * @param list<string> $aliases
     *
     * @throws ValidationException
     */
    public function addFlag(array $aliases, string $desc): self
    {
        if (count($this->usages) > 1) {
            throw new ValidationException('Cannot add flags after usages');
        }

        $this->optHandler->addFlag($aliases, $desc);

        return $this;
    }

    /**
     * A parameter has a required argument.
     *
     * @param list<string> $aliases
     *
     * @throws ValidationException
     */
    public function addParam(array $aliases, string $type, string $desc, ?callable $callback = null): self
    {
        if (count($this->usages) > 1) {
            throw new ValidationException('Cannot add params after usages');
        }

        $this->optHandler->addParam($aliases, $type, $desc, $callback);

        return $this;
    }

    /**
     * A term is a positional argument.
     *
     * @throws ValidationException
     */
    public function addTerm(string $name, string $type, string $desc, ?callable $callback = null): self
    {
        if (count($this->usages) > 1) {
            throw new ValidationException('Cannot add terms after usages');
        }

        $this->optHandler->addTerm($name, $type, $desc, $callback);

        return $this;
    }

    /**
     * Add a usage to the command by name.
     *
     * @param list<string> $optionNames
     */
    public function addUsage(array $optionNames): self
    {
        $hasCommand = false;
        foreach ($optionNames as $optionName) {
            if ($this->optHandler->getOptionType($optionName) === 'command') {
                $hasCommand = true;
                break;
            }
        }

        if (! $hasCommand) {
            $this->allCommands = false;
        }

        // Multiple usages besides help must define a command.
        if (count($this->usages) > 2 && ! $this->allCommands) {
            throw new ValidationException('Must define command for each usage');
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
        $filteredOptions = array_filter($optionNames, static fn($option): bool => $option !== 'help');

        $this->addUsage($filteredOptions);

        return $this;
    }

    /**
     * Check for errors then write them and exit.
     *
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    public function checkResult(OptResult $optResult): void
    {
        $errors = $optResult->getErrors();
        if ($errors === []) {
            return;
        }

        $message = 'Errors found in matching usage';
        $command = $optResult->getCommand();
        if ($command !== null) {
            $message .= ' for command "' . $command . '"';
        }

        $message .= ":\n";
        foreach ($errors as $error) {
            $message .= sprintf('* %s%s', $error, PHP_EOL);
        }

        $message .= "\n";
        $message .= 'Program terminating. Run again with -h for help.';
        error_log($message);
        if (! $this->debugMode) {
            exit;
        }
    }

    /**
     * @param ?string[] $args
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function parse(?array $args = null, bool $doResultCheck = true): OptResult
    {
        global $argv;
        if ($args === null) {
            $args = $argv;
        }

        $this->argParser = new ArgParser($args);
        $unmarkedOptions = $this->argParser->getUnmarkedOptions();
        $markedOptions = $this->argParser->getMarkedOptions();
        $nonOptions = $this->argParser->getNonOptions();

        // Get options except for help.
        $usages = $this->usages;
        array_shift($usages);

        // Check for help option and handle if found.
        $helpOption = $this->optHandler->getOption('help');

        foreach (array_keys($markedOptions) as $name) {
            if ($helpOption->matchName($name)) {
                $this->printHelp();
            }
        }

        $optResult = new OptResult($nonOptions);

        // The first unmarked input must be the command name.
        $inputName = null;
        if ($this->allCommands) {
            $inputName = array_shift($unmarkedOptions);
            if ($inputName === null) {
                $optResult->addError('Command name not provided');
                $this->checkResult($optResult);
            }
        }

        $matchFound = false;
        foreach ($usages as $usage) {
            // Match commands
            if ($this->allCommands && $inputName !== null) {
                $commandNames = $usage->getOptions('command');

                // There is only one command per usage.
                $commandName = $commandNames[0];
                $command = $this->optHandler->getOption($commandName);

                if ($command->matchName($inputName)) {
                    $optResult->setCommand($commandName, true);
                } else {
                    continue;
                }
            }

            // Match terms
            $termNames = $usage->getOptions('term');
            foreach ($termNames as $termName) {
                $inputValue = array_shift($unmarkedOptions);
                if ($inputValue === null) {
                    $optResult->addError('Missing term: "' . $termName . '"');
                    continue;
                }

                $term = $this->optHandler->getOption($termName);
                $matchedValue = $term->matchValue($inputValue);
                if ($matchedValue !== null) {
                    $optResult->setTerm($termName, $matchedValue);
                } else {
                    $optResult->addError(sprintf('Unable to match value of term "%s": "%s"', $termName, $inputValue));
                }
            }

            // Command and terms are all that is required to match.
            $matchFound = true;

            // Warn about unused unmarked options
            foreach ($unmarkedOptions as $optionName) {
                $optResult->addError('Unused input: "' . $optionName . '"');
            }

            // Match flags
            $flagNames = $usage->getOptions('flag');
            foreach ($flagNames as $flagName) {
                $flag = $this->optHandler->getOption($flagName);
                $found = false;
                $savedName = null;
                $savedValue = null;
                foreach ($markedOptions as $inputName => $inputValue) {
                    if ($flag->matchName($inputName)) {
                        $savedName = $inputName;
                        $savedValue = $inputValue;
                        $found = true;
                        break;
                    }
                }

                $optResult->setFlag($flagName, $found);

                if ($found) {
                    unset($markedOptions[$savedName]);
                    if ($savedValue !== '') {
                        $optResult->addError(sprintf('Argument passed to flag "%s": "%s"', $flagName, $savedValue));
                    }
                }
            }

            // Match params
            $paramNames = $usage->getOptions('param');
            foreach ($paramNames as $paramName) {
                $param = $this->optHandler->getOption($paramName);
                $found = false;
                $savedName = null;
                $savedValue = null;
                foreach ($markedOptions as $inputName => $inputValue) {
                    if ($param->matchName($inputName)) {
                        $savedName = $inputName;
                        $savedValue = $inputValue;
                        $found = true;
                        break;
                    }
                }

                if ($found) {
                    unset($markedOptions[$savedName]);
                    if ($savedValue === null) {
                        $optResult->addError('No value passed to param "' . $paramName . '"');
                    } else {
                        $matchedValue = $param->matchValue($savedValue);
                        if ($matchedValue !== null) {
                            $optResult->setParam($paramName, $matchedValue);
                        } else {
                            $optResult->addError(
                                sprintf('Unable to match value of param "%s": "%s"', $paramName, $savedValue)
                            );
                        }
                    }
                }
            }

            // Warn about unused marked options
            foreach ($markedOptions as $optionName => $optionValue) {
                $optResult->addError(sprintf('Unused input for "%s": "%s"', $optionName, $optionValue));
            }
        }

        if (! $matchFound) {
            $optResult->addError('Matching usage not found');
        }

        if ($doResultCheck) {
            $this->checkResult($optResult);
        }

        return $optResult;
    }

    /**
     * Print the program help, including:
     * - name
     * - description
     * - usage
     * - options
     *
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    protected function printHelp(): void
    {
        echo $this->name . "\n\n";
        echo wordwrap($this->desc) . "\n\n";
        echo "Usage:\n";
        $programName = $this->argParser->getProgramName();
        foreach ($this->usages as $usage) {
            echo $usage->write($programName);
        }

        echo "\n";

        echo $this->optHandler->writeOptionBlock();
        if (! $this->debugMode) {
            exit;
        }
    }
}

<?php

declare(strict_types=1);

namespace DouglasGreen\OptParser;

/**
 * Represent data from matching a usage.
 */
class OptResult
{
    /**
     * Errors, an array of error strings denoting missing, wrongly typed, or unrecognized arguments.
     *
     * @var list<string>
     */
    protected array $errors = [];

    /**
     * The match results, an associative array where keys are the name of the options
     * being matched and values are:
     * - true|false for commands
     * - value for terms
     * - true|false for flags
     * - value for params
     *
     * @var array<string, null|bool|float|int|string>
     */
    protected array $matchResults = [];

    protected ?string $command = null;

    /**
     * Constructor to initialize the OptionParserResult class with nonoptions.
     */
    public function __construct(
        /**
         * Nonoptions, an array of unrelated values.
         *
         * @var list<string> $nonoptions
         */
        protected array $nonoptions = []
    ) {}

    /**
     * Get the value of a match result, if any. Converts camel case to kebab
     * case to match option name.
     *
     * @todo Change to throw an exception once all values have been set.
     */
    public function __get(string $name): null|bool|float|int|string
    {
        // Convert camel case to kebab case
        $kebabCaseName = strtolower((string) preg_replace('/([a-z])([A-Z])/', '$1-$2', $name));

        return $this->matchResults[$kebabCaseName] ?? null;
    }

    /**
     * Add an error message.
     */
    public function addError(string $error): void
    {
        $this->errors[] = $error;
    }

    /**
     * Get the value of a match result, if any. No name conversion.
     */
    public function get(string $name): null|bool|float|int|string
    {
        return $this->matchResults[$name] ?? null;
    }

    /**
     * Get the command name, if any (there can only be one).
     */
    public function getCommand(): ?string
    {
        return $this->command;
    }

    /**
     * Get the errors.
     *
     * @return list<string>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get the match results.
     *
     * @return array<string, null|bool|float|int|string>
     */
    public function getMatchResults(): array
    {
        return $this->matchResults;
    }

    /**
     * Get the nonoptions.
     *
     * @return list<string>
     */
    public function getNonoptions(): array
    {
        return $this->nonoptions;
    }

    /**
     * Set the match result for a command.
     */
    public function setCommand(string $command, bool $value): void
    {
        $this->command = $command;
        $this->matchResults[$command] = $value;
    }

    /**
     * Set the match result for a flag.
     */
    public function setFlag(string $flag, bool $value): void
    {
        $this->matchResults[$flag] = $value;
    }

    /**
     * Set the match result for a param.
     */
    public function setParam(string $param, mixed $value): void
    {
        $this->matchResults[$param] = $value;
    }

    /**
     * Set the match result for a term.
     */
    public function setTerm(string $term, mixed $value): void
    {
        $this->matchResults[$term] = $value;
    }
}

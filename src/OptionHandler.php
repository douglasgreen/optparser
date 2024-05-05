<?php

namespace DouglasGreen\OptParser;

/**
 * Define and print options.
 */
class OptionHandler
{
    /** @var list<string> */
    protected static $validTypes = [
        'BOOL',
        'FLOAT',
        'INT',
        'STRING',
    ];

    /** @var array<string, bool> */
    protected $allAliases = [];

    /** @var array<string, bool> */
    protected $allNames = [];

    /**
     * @var array<string, array{
     *     aliases: list<string>,
     *     desc: string
     * }>
     */
    protected $commands = [];

    /**
     * @var array<string, array{
     *     aliases: list<string>,
     *     desc: string
     * }>
     */
    protected $flags = [];

    /**
     * @var array<string, array{
     *     aliases: list<string>,
     *     type: string,
     *     desc: string
     * }>
     */
    protected $params = [];

    /**
     * @var array<string, array{
     *      type: string,
     *      desc: string
     * }>
     */
    protected $terms = [];

    /**
     * A command is a predefined list of command words.
     *
     * @param list<string> $aliases
     */
    public function addCommand(array $aliases, string $desc): void
    {
        [$name, $others] = $this->pickName($aliases);
        $this->commands[$name] = [
            'aliases' => $others,
            'desc' => $desc
        ];
    }

    /**
     * A flag has no arguments.
     *
     * @param list<string> $aliases
     */
    public function addFlag(array $aliases, string $desc): void
    {
        [$name, $others] = $this->pickName($aliases);
        $this->flags[$name] = [
            'aliases' => $others,
            'desc' => $desc
        ];
    }

    /**
     * A parameter has a required argument.
     *
     * @param list<string> $aliases
     */
    public function addParam(
        array $aliases,
        string $type,
        string $desc
    ): void {
        [$name, $others] = $this->pickName($aliases);
        $type = strtoupper($type);
        $this->checkType($type);
        $this->params[$name] = [
            'aliases' => $others,
            'desc' => $desc,
            'type' => $type
        ];
    }

    /**
     * A term is a positional argument.
     */
    public function addTerm(string $name, string $type, string $desc): void
    {
        $this->checkAlias($name);
        $this->allNames[$name] = true;
        $this->terms[$name] = [
            'desc' => $desc,
            'type' => $type
        ];
    }

    /**
     * Has the name been defined?
     */
    public function hasName(string $name): bool
    {
        return isset($this->names[$name]);
    }

    /**
     * Write an option by name.
     *
     * @throws OptionParserException
     */
    public function writeOption(string $name): string
    {
        if (isset($this->commands[$name])) {
            return $name;
        }

        if (isset($this->params[$name])) {
            $param = $this->params[$name];
            return $this->hyphenate($name) . '=' . $param['type'];
        }

        if (isset($this->terms[$name])) {
            $term = $this->terms[$name];
            return $this->hyphenate($name) . ':' . $term['type'];
        }

        if (isset($this->flags[$name])) {
            return $this->hyphenate($name);
        }

        throw new OptionParserException("Name not found");
    }

    /**
     * Get a string representation of options, arguments, and descriptions.
     */
    public function writeOptionBlock(): string
    {
        $output = '';

        if ($this->commands) {
            $output .= $this->writeCommandBlock();
        }

        if ($this->params) {
            $output .= $this->writeParamBlock();
        }

        if ($this->flags) {
            $output .= $this->writeFlagBlock();
        }

        if ($this->terms) {
            $output .= $this->writeTermBlock();
        }

        return $output;
    }

    /**
     * Check alias for uniqueness.
     *
     * @throws OptionParserException
     */
    protected function checkAlias(string $alias): void
    {
        if (isset($this->allAliases[$alias])) {
            throw new OptionParserException("Duplicate alias: " . $alias);
        }
        $this->allAliases[$alias] = true;
    }

    /**
     * Check for supported types.
     *
     * @throws OptionParserException
     */
    protected function checkType(string $type): void
    {
        if (!in_array($type, self::$validTypes)) {
            throw new OptionParserException("Unsupported type: " . $type);
        }
    }

    protected function hyphenate(string $alias): string
    {
        if (strlen($alias) == 1) {
            return '-' . $alias;
        } else {
            return '--' . $alias;
        }
    }

    /**
     * @param list<string> $aliases
     * @return array{string, list<string>}
     * @throws OptionParserException
     */
    protected function pickName(array $aliases): array
    {
        $name = null;
        $others = [];
        foreach ($aliases as $alias) {
            $this->checkAlias($alias);
            if (!$name && strlen($alias) > 1) {
                $name = $alias;
                $this->allNames[$name] = true;
            } else {
                $others[] = $alias;
            }
        }

        if (!$name) {
            throw new OptionParserException("Missing required long name");
        }

        return [$name, $others];
    }

    protected function writeCommandBlock(): string
    {
        $output = "Commands:\n";
        foreach ($this->commands as $name => $command) {
            $output .= "  $name";
            foreach ($command['aliases'] as $alias) {
                $output .= " | $alias";
            }
            $output .= '  ' . $command['desc'] . "\n";
        }
        $output .= "\n";
        return $output;
    }

    protected function writeFlagBlock(): string
    {
        $output = "Flags:\n";
        foreach ($this->flags as $name => $flag) {
            $output .= '  ';
            $output .= $this->hyphenate($name);
            foreach ($flag['aliases'] as $alias) {
                $output .= ' | ';
                $output .= $this->hyphenate($alias);
            }
            $output .= '  ' . $flag['desc'] . "\n";
        }
        $output .= "\n";
        return $output;
    }

    protected function writeParamBlock(): string
    {
        $output = "Parameters:\n";
        foreach ($this->params as $name => $param) {
            $output .= '  ';
            $output .= $this->hyphenate($name);
            foreach ($param['aliases'] as $alias) {
                $output .= ' | ';
                $output .= $this->hyphenate($alias);
            }
            $output .= ' = ' . $param['type'];
            $output .= '  ' . $param['desc'] . "\n";
        }
        $output .= "\n";
        return $output;
    }

    protected function writeTermBlock(): string
    {
        $output = "Terms:\n";
        foreach ($this->terms as $name => $term) {
            $output .= "  $name: " . $term['type'] . '  ' . $term['desc'] . "\n";
        }
        $output .= "\n";
        return $output;
    }
}

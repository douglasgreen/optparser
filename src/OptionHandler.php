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
     * @var array<string, string>
     */
    protected $terms = [];

    /**
     * A flag has no arguments.
     *
     * @param array<string> $aliases
     */
    public function addFlag(array $aliases, string $desc): void
    {
        $name = $this->pickName($aliases);
        $this->checkAlias($name);
        $others = [];
        foreach ($aliases as $alias) {
            if ($alias != $name) {
                $this->checkAlias($alias);
                $others[] = $alias;
            }
        }
        $this->flags[$name] = [
            'aliases' => $others,
            'desc' => $desc
        ];
    }

    /**
     * A parameter has a required argument.
     *
     * @param array<string> $aliases
     */
    public function addParam(
        array $aliases,
        string $type,
        string $desc
    ): void {
        $name = $this->pickName($aliases);
        $this->checkAlias($name);
        $others = [];
        foreach ($aliases as $alias) {
            if ($alias != $name) {
                $this->checkAlias($alias);
                $others[] = $alias;
            }
        }
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
    public function addTerm(string $name, string $desc): void
    {
        $this->checkAlias($name);
        $this->terms[$name] = $desc;
    }

    /**
     * Print a list of options, arguments, and descriptions.
     */
    public function printOptions(): void
    {
        if ($this->params) {
            $this->printParams();
        }

        if ($this->flags) {
            $this->printFlags();
        }

        if ($this->terms) {
            $this->printTerms();
        }
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

    /**
     * @param array<string> $aliases
     * @throws OptionParserException
     */
    protected function pickName(array $aliases): string
    {
        $name = null;
        foreach ($aliases as $alias) {
            if (!$name && strlen($alias) > 1) {
                $name = $alias;
            }
        }

        if (!$name) {
            throw new OptionParserException("Missing required long name");
        }

        return $name;
    }

    protected function printAlias(string $alias): void
    {
        if (strlen($alias) == 1) {
            echo '-' . $alias;
        } else {
            echo '--' . $alias;
        }
    }

    protected function printFlags(): void
    {
        echo "Flags:\n";
        foreach ($this->flags as $name => $flag) {
            echo '  ';
            $this->printAlias($name);
            foreach ($flag['aliases'] as $alias) {
                echo ' | ';
                $this->printAlias($alias);
            }
            echo '  ' . $flag['desc'] . "\n";
        }
        echo "\n";
    }

    protected function printParams(): void
    {
        echo "Parameters:\n";
        foreach ($this->params as $name => $param) {
            echo '  ';
            $this->printAlias($name);
            foreach ($param['aliases'] as $alias) {
                echo ' | ';
                $this->printAlias($alias);
            }
            echo ' = ' . $param['type'];
            echo '  ' . $param['desc'] . "\n";
        }
        echo "\n";
    }

    protected function printTerms(): void
    {
        if ($this->terms) {
            echo "Terms:\n";
            foreach ($this->terms as $name => $desc) {
                echo "  $name  $desc\n";
            }
            echo "\n";
        }
    }
}

<?php

namespace DouglasGreen\OptParser;

/**
 * Define and print options.
 */
class OptionHandler
{
    /** @var array */
    protected $flags = [];

    /** @var array */
    protected $params = [];

    /** @var array */
    protected $terms = [];

    /** @var array */
    protected $allAliases = [];

    /**
     * A flag has no arguments.
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
        $this->flags[$name] = ['desc' => $desc, 'aliases' => $others];
    }

    /**
     * A parameter has a required argument.
     */
    public function addParam(array $aliases, string $arg, string $desc): void
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
        $this->params[$name] = ['desc' => $desc, 'aliases' => $others, 'arg' => $arg];
    }

    /**
     * A term is a positional argument.
     */
    public function addTerm(string $name, string $desc): void
    {
        $this->checkAlias($name):
        $this->terms[$name] = $desc;
    }

    /**
     * Print a list of options, arguments, and descriptions.
     */
    public function printOptions()
    {
        if ($this->params) {
            echo "Parameters:\n":
            foreach ($this->params as $name => $param) {
                echo "  ";
                $this->printAlias($name);
                foreach ($param['aliases'] as $alias) {
                    echo ' | ';
                    $this->printAlias($alias);
                }
                echo ' = ' . $param['arg'];
                echo '  ' . $param['desc'] . "\n";
            }
            echo "\n";
        }

        if ($this->flags) {
            echo "Flags:\n":
            foreach ($this->flags as $name => $flag) {
                echo "  ";
                $this->printAlias($name);
                foreach ($flag['aliases'] as $alias) {
                    echo ' | ';
                    $this->printAlias($alias);
                }
                echo '  ' . $flag['desc'] . "\n";
            }
            echo "\n";
        }

        if ($this->terms) {
            echo "Terms:\n";
            foreach ($terms as $name => $desc) {
                echo "  $name  $desc\n";
            }
            echo "\n";
        }
    }

    /**
     * Check alias for uniqueness.
     *
     * @throws OptParserException
     */
    protected function checkAlias(string $alias): string
    {
        if (isset($this->allAliases[$alias])) {
            throw new OptParserException("Duplicate alias: " . $alias);
        }
        $this->allAliases[$alias] = true;
    }

    /**
     * @throws OptParserException
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
            throw new OptParserException("Missing required long name");
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
}

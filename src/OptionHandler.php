<?php

namespace DouglasGreen\OptionParser;

/**
 * Define and print options.
 */
class OptionHandler
{
    /** @var array */
    protected $hasExtras = [
        'flag' => false,
        'param' => false,
        'term' => false
    ];

    /** @var array */
    protected $allAliases = [];

    /** @var array */
    protected $flags = [];

    /** @var array */
    protected $params = [];

    /** @var array */
    protected $terms = [];

    public function addExtraFlag(array $aliases, string $desc): void
    {
        $this->hasExtras['flag'] = true;
        $this->addFlag($aliases, $desc, false):
    }

    public function addRequiredFlag(array $aliases, string $desc): void
    {
        if ($this->hasExtras['flag']) {
            throw new OptionParserException("Required flag added after extra flag");
        }
        $this->addFlag($aliases, $desc, true):
    }

    public function addExtraParam(array $aliases, string $arg, string $desc): void
    {
        $this->hasExtras['param'] = true;
        $this->addParam($aliases, $arg, $desc, false):
    }

    public function addRequiredParam(array $aliases, string $arg, string $desc): void
    {
        if ($this->hasExtras['param']) {
            throw new OptionParserException("Required param added after extra param");
        }
        $this->addParam($aliases, $arg, $desc, true):
    }

    public function addExtraTerm(string $name, string $desc): void
    {
        $this->hasExtras['term'] = true;
        $this->addTerm($name, $desc, false):
    }

    public function addRequiredTerm(string $name, string $desc): void
    {
        if ($this->hasExtras['term']) {
            throw new OptionParserException("Required term added after extra term");
        }
        $this->addTerm($name, $desc, true):
    }

    /**
     * Print a list of options, arguments, and descriptions.
     */
    public function printOptions()
    {
        if ($this->params) {
            echo "Parameters:\n":
            foreach ($this->params as $name => $param) {
                echo '  ';
                if (!$param['required']) {
                    echo '<';
                }
                $this->printAlias($name);
                foreach ($param['aliases'] as $alias) {
                    echo ' | ';
                    $this->printAlias($alias);
                }
                echo ' = ' . $param['arg'];
                if (!$param['required']) {
                    echo '<';
                }
                echo '  ' . $param['desc'] . "\n";
            }
            echo "\n";
        }

        if ($this->flags) {
            echo "Flags:\n":
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

        if ($this->terms) {
            echo "Terms:\n";
            foreach ($terms as $name => $desc) {
                echo "  $name  $desc\n";
            }
            echo "\n";
        }
    }

    /**
     * A flag has no arguments.
     */
    protected function addFlag(array $aliases, string $desc, bool $required): void
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
            'desc' => $desc,
            'aliases' => $others,
            'required'=> $required
        ];
    }

    /**
     * A parameter has a required argument.
     */
    protected function addParam(
        array $aliases,
        string $arg,
        string $desc,
        bool $required
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
        $this->params[$name] = [
            'desc' => $desc,
            'aliases' => $others,
            'arg' => $arg,
            'required'=> $required
        ];
    }

    /**
     * A term is a positional argument.
     */
    protected function addTerm(string $name, string $desc, bool $required): void
    {
        $this->checkAlias($name):
        $this->terms[$name] = [
            'desc' => $desc,
            'required'=> $required
        ];
    }

    /**
     * Check alias for uniqueness.
     *
     * @throws OptionParserException
     */
    protected function checkAlias(string $alias): string
    {
        if (isset($this->allAliases[$alias])) {
            throw new OptionParserException("Duplicate alias: " . $alias);
        }
        $this->allAliases[$alias] = true;
    }

    /**
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
}

<?php

declare(strict_types=1);

namespace DouglasGreen\OptParser\Option;

use DouglasGreen\OptParser\OptParserException;

abstract class Option
{
    /**
     * @var list<string>
     *
     * @todo Validate using https://www.php.net/manual/en/filter.filters.validate.php
     */
    protected const VALID_TYPES = ['BOOL', 'EMAIL', 'FLOAT', 'INT', 'IP', 'REGEXP', 'STRING', 'URL'];

    /**
     * @var ?list<string>
     */
    protected $aliases;

    /**
     * @var ?string
     */
    protected $regexp;

    /**
     * @var ?string
     */
    protected $type;

    public function __construct(
        protected string $name,
        protected string $desc
    ) {
    }

    public function castBool(string $value): ?bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }

    /**
     * @return ?list<string>
     */
    public function getAliases(): ?array
    {
        return $this->aliases;
    }

    public function getDesc(): string
    {
        return $this->desc;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function hasAlias(string $alias): bool
    {
        return $this->aliases && in_array($alias, $this->aliases, true);
    }

    public function hyphenate(string $alias): string
    {
        if (strlen($alias) === 1) {
            return '-' . $alias;
        }

        return '--' . $alias;
    }

    abstract public function matchInput(string $value): bool;

    abstract public function write(): string;

    /**
     * Check for supported types.
     */
    protected function checkType(string $type): void
    {
        if (! in_array($type, self::VALID_TYPES, true)) {
            throw new OptParserException('Unsupported type: ' . $type);
        }
    }

    protected function setType(string $type, ?string $regexp = null): void
    {
        $type = strtoupper($type);
        $this->checkType($type);
        $this->type = $type;
        if ($this->type === 'REGEXP') {
            $this->regexp = $regexp;
        }
    }
}

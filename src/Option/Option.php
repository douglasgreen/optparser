<?php

namespace DouglasGreen\OptParser\Option;

use DouglasGreen\OptParser\OptParserException;

abstract class Option
{
   /** @var list<string> */
    protected const VALID_TYPES = [
        'BOOL',
        'FLOAT',
        'INT',
        'STRING',
    ];

    /** @var ?list<string> */
    protected $aliases;

    /** @var string */
    protected $name;

    /** @var string */
    protected $desc;

    /** @var ?string */
    protected $type;

    public function __construct(string $name, string $desc)
    {
        $this->name = $name;
        $this->desc = $desc;
    }

    public function castBool(string $value): ?bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }

    /** @return ?list<string> */
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
        return $this->aliases && in_array($alias, $this->aliases);
    }

    public function hyphenate(string $alias): string
    {
        if (strlen($alias) == 1) {
            return '-' . $alias;
        } else {
            return '--' . $alias;
        }
    }

    abstract public function matchInput(string $value): bool;

    abstract public function write(): string;

    /**
     * Check for supported types.
     *
     * @throws OptParserException
     */
    protected function checkType(string $type): void
    {
        if (!in_array($type, self::VALID_TYPES)) {
            throw new OptParserException("Unsupported type: " . $type);
        }
    }

    protected function setType(string $type): void
    {
        $type = strtoupper($type);
        $this->checkType($type);
        $this->type = $type;
    }
}

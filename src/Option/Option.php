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
    ) {}

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

    abstract public function write(): string;

    protected function castBool(string $value): ?bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }

    protected function castFloat(string $value): ?float
    {
        return filter_var($value, FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE);
    }

    protected function castInt(string $value): ?int
    {
        return filter_var($value, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
    }

    protected function castRegexp(string $value, string $regexp): ?string
    {
        $options = [
            'options' => [
                'regexp' => $regexp,
            ],
        ];

        $result = filter_var($value, FILTER_VALIDATE_REGEXP, $options);

        return $result !== false ? $result : null;
    }

    /**
     * @throws OptParserException
     */
    protected function castString(string $value, string $type): ?string
    {
        if ($type === 'EMAIL') {
            return filter_var($value, FILTER_VALIDATE_EMAIL, FILTER_NULL_ON_FAILURE);
        }

        if ($type === 'IP') {
            return filter_var($value, FILTER_VALIDATE_IP, FILTER_NULL_ON_FAILURE);
        }

        if ($type === 'URL') {
            return filter_var($value, FILTER_VALIDATE_URL, FILTER_NULL_ON_FAILURE);
        }

        throw new OptParserException('Unsupported type');
    }

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

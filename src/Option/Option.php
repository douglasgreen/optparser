<?php

declare(strict_types=1);

namespace DouglasGreen\OptParser\Option;

use DouglasGreen\OptParser\OptParserException;

abstract class Option
{
    /**
     * @var list<string>
     *
     * @see Validate using https://www.php.net/manual/en/filter.filters.validate.php
     */
    protected const ARG_TYPES = [
        'BOOL',
        'DOMAIN',
        'EMAIL',
        'FLOAT',
        'INT',
        'IP_ADDR',
        'MAC_ADDR',
        'REGEXP',
        'STRING',
        'URL',
    ];

    /**
     * @var ?list<string>
     */
    protected $aliases;

    /**
     * @var ?string Type of the argument
     */
    protected $argType;

    /**
     * @var ?string
     */
    protected $regexp;

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

    public function getArgType(): ?string
    {
        return $this->argType;
    }

    public function getDesc(): string
    {
        return $this->desc;
    }

    public function getName(): string
    {
        return $this->name;
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

    protected function castDomain(string $value): ?string
    {
        return filter_var($value, FILTER_VALIDATE_DOMAIN, FILTER_NULL_ON_FAILURE);
    }

    protected function castEmail(string $value): ?string
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL, FILTER_NULL_ON_FAILURE);
    }

    protected function castFloat(string $value): ?float
    {
        return filter_var($value, FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE);
    }

    protected function castInt(string $value): ?int
    {
        return filter_var($value, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
    }

    protected function castIpAddress(string $value): ?string
    {
        return filter_var($value, FILTER_VALIDATE_IP, FILTER_NULL_ON_FAILURE);
    }

    protected function castMacAddress(string $value): ?string
    {
        return filter_var($value, FILTER_VALIDATE_MAC, FILTER_NULL_ON_FAILURE);
    }

    protected function castRegexp(string $value, ?string $regexp): ?string
    {
        if ($regexp === null) {
            return null;
        }

        $options = [
            'options' => [
                'regexp' => $regexp,
            ],
        ];

        $result = filter_var($value, FILTER_VALIDATE_REGEXP, $options);

        return $result !== false ? $result : null;
    }

    protected function castUrl(string $value): ?string
    {
        return filter_var($value, FILTER_VALIDATE_URL, FILTER_NULL_ON_FAILURE);
    }

    protected function castValue(string $value): string|float|int|bool|null
    {
        return match ($this->argType) {
            'BOOL' => $this->castBool($value),
            'DOMAIN' => $this->castDomain($value),
            'EMAIL' => $this->castEmail($value),
            'FLOAT' => $this->castFloat($value),
            'INT' => $this->castInt($value),
            'IP_ADDR' => $this->castIpAddress($value),
            'MAC_ADDR' => $this->castMacAddress($value),
            'REGEXP' => $this->castRegexp($value, $this->regexp),
            'URL' => $this->castUrl($value),
            default => null,
        };
    }

    /**
     * Check for supported types.
     */
    protected function checkType(string $argType): void
    {
        if (! in_array($argType, self::ARG_TYPES, true)) {
            throw new OptParserException('Unsupported argument type: ' . $argType);
        }
    }

    protected function setArgType(string $argType, ?string $regexp = null): void
    {
        $argType = strtoupper($argType);
        $this->checkType($argType);
        $this->argType = $argType;
        if ($this->argType === 'REGEXP') {
            $this->regexp = $regexp;
        }
    }
}

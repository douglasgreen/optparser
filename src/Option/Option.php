<?php

declare(strict_types=1);

namespace DouglasGreen\OptParser\Option;

use DouglasGreen\OptParser\OptParserException;

abstract class Option
{
    /**
     * @var list<string>
     *
     * Most types are just filters. Other types include:
     * - DIR - directory that must exist and be readable
     * - INFILE - input file that must exist and be readable
     * - OUTFILE - output file that must not exist and be writable
     *
     * @see https://www.php.net/manual/en/filter.filters.validate.php
     */
    protected const ARG_TYPES = [
        'BOOL',
        'DIR',
        'DOMAIN',
        'EMAIL',
        'FLOAT',
        'INFILE',
        'INT',
        'IP_ADDR',
        'MAC_ADDR',
        'OUTFILE',
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

    protected ?\Closure $callback = null;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function __construct(
        protected string $name,
        protected string $desc,
        ?callable $callback = null
    ) {
        if ($callback !== null) {
            $this->callback = \Closure::fromCallable($callback);
        }
    }

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

    public function hyphenate(string $alias): string
    {
        if (strlen($alias) === 1) {
            return '-' . $alias;
        }

        return '--' . $alias;
    }

    public function matchName(string $name): bool
    {
        return $this->name === $name || $this->hasAlias($name);
    }

    public function matchValue(string $value): bool|int|float|string|null
    {
        $filtered = match ($this->argType) {
            'BOOL' => $this->castBool($value),
            'DIR' => $this->checkDir($value),
            'DOMAIN' => $this->castDomain($value),
            'EMAIL' => $this->castEmail($value),
            'FLOAT' => $this->castFloat($value),
            'INFILE' => $this->checkInputFile($value),
            'INT' => $this->castInt($value),
            'IP_ADDR' => $this->castIpAddress($value),
            'MAC_ADDR' => $this->castMacAddress($value),
            'OUTFILE' => $this->checkOutputFile($value),
            'STRING' => $value,
            'URL' => $this->castUrl($value),
            default => null,
        };

        // Check for failure of basic type
        if ($filtered === null) {
            return $filtered;
        }

        // Apply callback to validate if available
        if ($this->callback instanceof \Closure) {
            return ($this->callback)($filtered);
        }

        return $filtered;
    }

    abstract public function write(): string;

    /**
     * @throws OptParserException
     */
    protected function addAlias(string $alias): void
    {
        // Only matches lower case separated by hyphens
        if (preg_match('/^[a-z][a-z0-9]*(-[a-z0-9]+)*$/', $alias) === 0) {
            throw new OptParserException('Alias is not hyphenated lower case: ' . $alias);
        }

        $this->aliases[] = $alias;
    }

    protected function castBool(string $value): ?bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }

    protected function castDomain(string $value): ?string
    {
        return filter_var($value, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME | FILTER_NULL_ON_FAILURE);
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

    protected function castUrl(string $value): ?string
    {
        return filter_var($value, FILTER_VALIDATE_URL, FILTER_NULL_ON_FAILURE);
    }

    /**
     * Check that the dir is readable then form the dir path.
     */
    protected function checkDir(string $value): ?string
    {
        if (is_dir($value) && is_readable($value)) {
            $path = realpath($value);
            if ($path !== false) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Check that the input file is readable then form the file path.
     */
    protected function checkInputFile(string $value): ?string
    {
        if (file_exists($value) && is_readable($value)) {
            $path = realpath($value);
            if ($path !== false) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Check that the parent directory is writable then form the new file path.
     */
    protected function checkOutputFile(string $value): ?string
    {
        $directory = dirname($value);
        if (is_writable($directory)) {
            $path = realpath($directory);
            if ($path !== false) {
                return $path . (DIRECTORY_SEPARATOR . basename($value));
            }
        }

        // If the file exists or the location is not writable, return null
        return null;
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

    protected function hasAlias(string $alias): bool
    {
        return $this->aliases && in_array($alias, $this->aliases, true);
    }

    protected function setArgType(string $argType): void
    {
        $argType = strtoupper($argType);
        $this->checkType($argType);
        $this->argType = $argType;
    }
}

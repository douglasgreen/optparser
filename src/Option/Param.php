<?php

declare(strict_types=1);

namespace DouglasGreen\OptParser\Option;

class Param extends Option
{
    /**
     * @param list<string> $aliases
     */
    public function __construct(
        string $name,
        string $desc,
        array $aliases,
        string $argType,
        ?string $regexp = null
    ) {
        parent::__construct($name, $desc);
        $this->aliases = $aliases;
        $this->setArgType($argType, $regexp);
    }

    public function matchInput(string $name, string $value): string|float|int|bool|null
    {
        if ($this->name !== $name && ! $this->hasAlias($name)) {
            return null;
        }

        return $this->castValue($value);
    }

    #[\Override]
    public function write(): string
    {
        return $this->hyphenate($this->name) . '=' . $this->argType;
    }
}

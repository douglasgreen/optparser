<?php

declare(strict_types=1);

namespace DouglasGreen\OptParser\Option;

class Flag extends Option
{
    /**
     * @param list<string> $aliases
     */
    public function __construct(
        string $name,
        string $desc,
        array $aliases
    ) {
        parent::__construct($name, $desc);
        $this->aliases = $aliases;
    }

    public function matchInput(string $name): bool
    {
        return $this->name === $name || $this->hasAlias($name);
    }

    #[\Override]
    public function write(): string
    {
        return $this->hyphenate($this->name);
    }
}

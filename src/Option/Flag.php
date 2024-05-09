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

    /**
     * @todo Finish
     */
    public function matchInput(string $value): bool
    {
        return (bool) $value;
    }

    #[\Override]
    public function write(): string
    {
        return $this->hyphenate($this->name);
    }
}

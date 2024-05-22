<?php

declare(strict_types=1);

namespace DouglasGreen\OptParser\Option;

class Command extends Option
{
    /**
     * @param list<string> $aliases
     */
    public function __construct(string $name, string $desc, array $aliases)
    {
        parent::__construct($name, $desc);
        $this->aliases = $aliases;
    }

    #[\Override]
    public function write(): string
    {
        return $this->name;
    }
}

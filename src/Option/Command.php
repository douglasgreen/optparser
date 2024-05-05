<?php

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

    public function write(): string
    {
        return $this->name;
    }
}

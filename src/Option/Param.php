<?php

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
        string $type
    ) {
        parent::__construct($name, $desc);
        $this->aliases = $aliases;
        $this->setType($type);
    }

    /** @todo Finish */
    public function matchInput(string $value): bool
    {
        return false;
    }

    public function write(): string
    {
        return $this->hyphenate($this->name) . '=' . $this->type;
    }
}

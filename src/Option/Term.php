<?php

declare(strict_types=1);

namespace DouglasGreen\OptParser\Option;

class Term extends Option
{
    public function __construct(
        string $name,
        string $desc,
        string $argType,
        ?string $regexp = null
    ) {
        parent::__construct($name, $desc);
        $this->setArgType($argType, $regexp);
    }

    #[\Override]
    public function write(): string
    {
        return $this->name . ':' . $this->argType;
    }
}

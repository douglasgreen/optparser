<?php

declare(strict_types=1);

namespace DouglasGreen\OptParser\Option;

class Term extends Option
{
    public function __construct(
        string $name,
        string $desc,
        string $type,
        ?string $regexp = null
    ) {
        parent::__construct($name, $desc);
        $this->setType($type, $regexp);
    }

    #[\Override]
    public function matchInput(string $value): bool
    {
        if ($this->getType() === 'BOOL') {
            $result = $this->castBool($value);
            if ($result === null) {
                return false;
            }
        }

        return false;
    }

    #[\Override]
    public function write(): string
    {
        return $this->name . ':' . $this->type;
    }
}

<?php

namespace DouglasGreen\OptParser\Option;

class Term extends Option
{
    public function __construct(
        string $name,
        string $desc,
        string $type,
        string $regexp = null
    ) {
        parent::__construct($name, $desc);
        $this->setType($type, $regexp);
    }

    public function matchInput(string $value): bool
    {
        switch ($this->getType()) {
            case 'BOOL':
                $result = $this->castBool($value);
                if ($result === null) {
                    return false;
                }
                break;
        }
        return false;
    }

    public function write(): string
    {
        return $this->name . ':' . $this->type;
    }
}

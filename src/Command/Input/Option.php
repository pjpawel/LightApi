<?php

namespace pjpawel\LightApi\Command\Input;

class Option
{

    public const OPTIONAL = 1;
    public const REQUIRED = 2;

    public string $name;
    public string $shortcut;
    public int $type;
    public mixed $default;
    public ?string $description;
    public ?string $value = null;

    public function __construct(
        string $name,
        string $shortcut,
        int $type = Option::OPTIONAL,
        mixed $default = null,
        string $description = null
    )
    {
        $this->name = $name;
        $this->shortcut = $shortcut;
        $this->type = $type;
        $this->default = $default;
        $this->description = $description;
    }

    /**
     * @param string|null $value
     */
    public function setValue(?string $value): void
    {
        $this->value = $value;
    }
}
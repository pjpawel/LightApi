<?php

namespace pjpawel\LightApi\Command\Input;

class Argument
{

    public const REQUIRED = 1;
    public const OPTIONAL = 2;
    public const IS_ARRAY = 4;

    public string $name;
    public int $type;
    public ?string $description;
    public ?string $value = null;

    public function __construct(string $name, int $type = self::REQUIRED, ?string $description = null)
    {
        $this->name = $name;
        $this->type = $type;
        $this->description = $description;
    }

    /**
     * @param string $value
     */
    public function setValue(string $value): void
    {
        $this->value = $value;
    }

}
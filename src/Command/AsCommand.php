<?php

namespace pjpawel\LightApi\Command;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class AsCommand
{

    public string $name;
    public bool $prod;

    public function __construct(string $name, bool $prod = true)
    {
        $this->name = $name;
        $this->prod = $prod;
    }

}
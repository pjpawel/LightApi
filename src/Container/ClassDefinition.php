<?php

namespace pjpawel\LightApi\Container;

class ClassDefinition extends Definition
{

    public string $name;
    /**
     * @var string[]
     */
    public array $arguments;

    /**
     * @param string $name
     * @param string[] $arguments
     */
    public function __construct(string $name, array $arguments)
    {
        $this->name = $name;
        $this->arguments = $arguments;
    }
}
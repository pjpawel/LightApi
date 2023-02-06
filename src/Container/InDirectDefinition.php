<?php

namespace pjpawel\LightApi\Container;

class InDirectDefinition extends Definition
{
    public string $className;

    /**
     * @param string $name
     * @param string $className
     */
    public function __construct(string $name, string $className)
    {
        $this->name = $name;
        $this->className = $className;
    }
}
<?php

namespace pjpawel\LightApi\Component\Event;

class DefinedFunctionEvent implements EventInterface
{

    private string $functionName;
    private array $arguments;

    public function __construct(string $functionName, $arguments)
    {
        $this->functionName = $functionName;
        $this->arguments = $arguments;
    }

    public function run(): mixed
    {
        return call_user_func($this->functionName, $this->arguments);
    }
}
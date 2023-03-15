<?php

namespace pjpawel\LightApi\Component\Event;

class MethodEvent implements EventInterface
{

    private string|object $className;
    private string $methodName;
    private array $arguments;

    public function __construct(string|object $className, string $methodName, array $arguments)
    {
        $this->className = $className;
        $this->methodName = $methodName;
        $this->arguments = $arguments;
    }

    public function run(): mixed
    {
        return call_user_func([$this->className, $this->methodName], $this->arguments);
    }
}
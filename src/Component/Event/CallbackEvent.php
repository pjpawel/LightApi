<?php

namespace pjpawel\LightApi\Component\Event;

class CallbackEvent implements EventInterface
{

    private \Closure $closure;
    private array $arguments;

    public function __construct(callable $function, array $arguments = [])
    {
        $this->closure = $function(...);
        $this->arguments = $arguments;
    }

    public function run(): mixed
    {
        return call_user_func($this->closure, $this->arguments);
    }
}
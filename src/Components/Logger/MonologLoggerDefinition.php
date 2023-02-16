<?php

namespace pjpawel\LightApi\Components\Logger;

use Monolog\Logger;
use pjpawel\LightApi\Container\DefinedDefinition;
use ReflectionClass;

class MonologLoggerDefinition extends DefinedDefinition
{

    public string $name;
    public array $handlers;
    public array $processors;

    public function __construct(string $name, array $handlers = [], array $processors = [])
    {
        $this->name = $name;
        $this->handlers = $handlers;
        $this->processors = $processors;
    }

    public function load(): object
    {
        $logger = new Logger($this->name);
        foreach ($this->handlers as $handler) {
            $logger->pushHandler($this->loadObjectFromConfig($handler));
        }
        foreach ($this->processors as $processor) {
            $logger->pushProcessor($this->loadObjectFromConfig($processor));
        }
        return $logger;
    }

    protected function loadObjectFromConfig(array $config): object
    {
        if (isset($config['args'])) {
            $reflectionHandler = new ReflectionClass($config['class']);
            return $reflectionHandler->newInstanceArgs($config['args']);
        } else {
            return new $config['class']();
        }
    }
}
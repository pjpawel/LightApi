<?php

namespace pjpawel\LightApi\Components\Logger;

use pjpawel\LightApi\Components\Extension\Extension;
use pjpawel\LightApi\Container\ContainerLoader;
use pjpawel\LightApi\Kernel\ProgrammerException;

class MonologExtension extends Extension
{

    private const CONTAINER_PREFIX = 'monolog.';

    public function registerServices(ContainerLoader $container): void
    {
        /** @var array<string, MonologLoggerDefinition> $definitions */
        $definitions = [];
        foreach ($this->config as $loggerName => $loggerConfig) {
            if (isset($loggerConfig['class']) && str_starts_with($loggerConfig['class'], '@')) {
                $loggerName = substr($loggerConfig['class'], 1);
                $definition = $this->getDefinitionFrom($definitions, $loggerName);
                $aliasHandlers = $definition->handlers;
                $aliasProcessors = $definition->processors;
            }
            $handlers = $loggerConfig['handlers'];
            $processors = $loggerConfig['processors'];
            if (isset($aliasHandlers)) {
                $handlers += $aliasHandlers;
            }
            if (isset($aliasProcessors)) {
                $processors += $aliasProcessors;
            }
            $definitions[self::CONTAINER_PREFIX . $loggerName] = new MonologLoggerDefinition($loggerName, $handlers, $processors);
        }
        $container->addDefinitions($definitions);
    }

    /**
     * @param array<string,MonologLoggerDefinition> $definitions
     * @param string $loggerName
     * @return MonologLoggerDefinition
     * @throws ProgrammerException
     */
    private function getDefinitionFrom(array $definitions, string $loggerName): MonologLoggerDefinition
    {
        if (isset($definitions[self::CONTAINER_PREFIX . $loggerName])) {
            return $definitions[self::CONTAINER_PREFIX . $loggerName];
        }
        throw new ProgrammerException('Cannot find logger definition of ' . $loggerName);
    }

}
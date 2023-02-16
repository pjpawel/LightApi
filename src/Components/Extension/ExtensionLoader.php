<?php

namespace pjpawel\LightApi\Components\Extension;

use pjpawel\LightApi\Command\CommandLoader;
use pjpawel\LightApi\Container\ContainerLoader;
use pjpawel\LightApi\Endpoint\EndpointsLoader;

class ExtensionLoader
{

    /**
     * @var array<string|int,array>
     */
    private array $extensionConfigs;

    /**
     * @param array<string|int, array> $extensionConfigs
     */
    public function __construct(array $extensionConfigs)
    {
        $this->extensionConfigs = $extensionConfigs;
    }

    public function loadExtensions(
        ContainerLoader $container,
        EndpointsLoader $endpointsLoader,
        CommandLoader $commandLoader
    ): void
    {
        foreach ($this->extensionConfigs as $extensionName => $extensionConfig) {
            if (is_int($extensionName)){
                $extensionClass = $extensionConfig;
                $extensionConfig = [];
            } else {
                $extensionClass = $extensionName;
            }
            /** @var ExtensionInterface $extension */
            $extension = new $extensionClass();
            $extension->loadConfig($extensionConfig);
            $extension->registerServices($container);
            $extension->registerRoutes($endpointsLoader);
            $extension->registerCommands($commandLoader);
        }
    }
}
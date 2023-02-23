<?php

namespace pjpawel\LightApi\Component\Extension;

use pjpawel\LightApi\Command\CommandsLoader;
use pjpawel\LightApi\Container\ContainerLoader;
use pjpawel\LightApi\Endpoint\EndpointsLoader;

interface ExtensionInterface
{

    public function loadConfig(array $config): void;

    public function registerServices(ContainerLoader $container): void;

    public function registerRoutes(EndpointsLoader $endpointsLoader): void;

    public function registerCommands(CommandsLoader $commandLoader): void;

}
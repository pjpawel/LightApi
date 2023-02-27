<?php

namespace pjpawel\LightApi\Component\Debug;

use pjpawel\LightApi\Command\CommandsLoader;
use pjpawel\LightApi\Container\ContainerLoader;
use pjpawel\LightApi\Route\Router;
use pjpawel\LightApi\Kernel;
use ReflectionClass;

class KernelDebugger
{

    public Kernel $kernel;

    public function __construct(Kernel $kernel)
    {
        $this->kernel = $kernel;
    }

    public function show(): void
    {
        $reflectionKernel = new ReflectionClass($this->kernel);

        $endpointsLoaderReflection = $reflectionKernel->getProperty('endpointsLoader');
        /** @var Router $endpointsLoader */
        $endpointsLoader = $endpointsLoaderReflection->getValue($this->kernel);
        var_dump($endpointsLoader->routes);

        $commandLoaderReflection = $reflectionKernel->getProperty('commandLoader');
        /** @var CommandsLoader $commandLoader */
        $commandLoader = $commandLoaderReflection->getValue($this->kernel);
        var_dump($commandLoader->command);

        $containerLoaderReflection = $reflectionKernel->getProperty('containerLoader');
        /** @var ContainerLoader $containerLoader */
        $containerLoader = $containerLoaderReflection->getValue($this->kernel);
        var_dump($containerLoader->definitions);
    }
}
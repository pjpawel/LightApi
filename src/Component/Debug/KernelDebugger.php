<?php

namespace pjpawel\LightApi\Component\Debug;

use pjpawel\LightApi\Command\CommandLoader;
use pjpawel\LightApi\Container\ContainerLoader;
use pjpawel\LightApi\Endpoint\EndpointsLoader;
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
        /** @var EndpointsLoader $endpointsLoader */
        $endpointsLoader = $endpointsLoaderReflection->getValue($this->kernel);
        $endpointsLoader->load($this->kernel->projectDir);
        var_dump($endpointsLoader->serialize());

        $commandLoaderReflection = $reflectionKernel->getProperty('commandLoader');
        /** @var CommandLoader $commandLoader */
        $commandLoader = $commandLoaderReflection->getValue($this->kernel);
        $commandLoader->load();
        var_dump($commandLoader->command);

        $containerLoaderReflection = $reflectionKernel->getProperty('containerLoader');
        /** @var ContainerLoader $containerLoader */
        $containerLoader = $containerLoaderReflection->getValue($this->kernel);
        var_dump($containerLoader->definitions);
    }
}
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

        $routerReflection = $reflectionKernel->getProperty('router');
        /** @var Router $router */
        $router = $routerReflection->getValue($this->kernel);
        var_dump($router->routes);

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
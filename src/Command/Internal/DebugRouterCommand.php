<?php

namespace pjpawel\LightApi\Command\Internal;

use pjpawel\LightApi\Command\Input\InputInterface;
use pjpawel\LightApi\Command\Output\OutputInterface;
use pjpawel\LightApi\Route\Router;
use ReflectionClass;

class DebugRouterCommand extends KernelAwareCommand
{

    /**
     * @inheritDoc
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $reflectionKernel = new ReflectionClass($this->kernel);
        $routerReflection = $reflectionKernel->getProperty('router');
        /** @var Router $router */
        $router = $routerReflection->getValue($this->kernel);

        $names = [];
        foreach ($router->routes as $route) {
            $names[] = $route->path;
        }

        $output->writeln($names);

        return self::SUCCESS;
    }
}
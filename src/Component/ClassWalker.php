<?php

namespace pjpawel\LightApi\Component;

use pjpawel\LightApi\Command\AsCommand;
use pjpawel\LightApi\Command\CommandsLoader;
use pjpawel\LightApi\Container\ContainerLoader;
use pjpawel\LightApi\Route\AsRoute;
use pjpawel\LightApi\Route\Router;
use pjpawel\LightApi\Exception\ProgrammerException;
use ReflectionClass;

class ClassWalker
{

    private string $servicePath;

    public function __construct(string $servicePath)
    {
        $this->servicePath = $servicePath;
    }

    /**
     * @param ContainerLoader $containerLoader
     * @param Router $router
     * @param CommandsLoader $commandsLoader
     * @return void
     * @throws ProgrammerException
     * @throws \ReflectionException
     */
    public function register(
        ContainerLoader $containerLoader,
        Router          $router,
        CommandsLoader  $commandsLoader
    ): void
    {
        if (is_file($this->servicePath) || !is_dir($this->servicePath)) {
            //TODO: this will be covered soon
            throw new ProgrammerException('Cannot load path that is not directory');
        }
        $classFinder = new ClassFinder();
        foreach ($classFinder->getAllClassInDir($this->servicePath) as $className) {
            /* Add to container */
            $containerLoader->add(['name' => $className]);
            /* Add routes or command if exists */
            $reflectionClass = new ReflectionClass($className);
            $commandAttribute = $reflectionClass->getAttributes(AsCommand::class);
            if (!empty($commandAttribute)) {
                $commandsLoader->registerCommand($commandAttribute[0]->getArguments()[0], $className);
            }
            foreach ($reflectionClass->getMethods() as $method) {
                $attributes = $method->getAttributes(AsRoute::class);
                if (!empty($attributes)) {
                    $attribute = $attributes[0];
                    $arguments = $attribute->getArguments();
                    $router->registerRoute(
                        $className,
                        $method->getName(),
                        $arguments[0],
                        $arguments[1] ?? []);
                }
            }
        }
    }
}
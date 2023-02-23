<?php

namespace pjpawel\LightApi\Command;

use pjpawel\LightApi\Command\Input\Stdin;
use pjpawel\LightApi\Command\Output\Stdout;
use pjpawel\LightApi\Container\ContainerLoader;
use pjpawel\LightApi\Container\ContainerNotFoundException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;

class CommandsLoader
{

    /**
     * @var array<string,string>
     */
    public array $command = [];
    public bool $loaded = true;


    public function registerCommand(string $commandName, string $className): void
    {
        $this->command[$commandName] = $className;
    }

    /**
     * @param string $commandName
     * @param ContainerLoader $container
     * @return int
     * @throws ContainerExceptionInterface
     * @throws ContainerNotFoundException
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException|\pjpawel\LightApi\Exception\ProgrammerException
     */
    public function runCommandFromName(string $commandName, ContainerLoader $container): int
    {
        $className = $this->command[$commandName];
        $reflectionClass = new ReflectionClass($className);
        $constructor = $reflectionClass->getConstructor();
        $args = [];
        foreach ($constructor->getParameters() as $parameter) {
            $args[] = $container->get($parameter->getType()->getName());
        }
        /* Prepare input */
        $stdin = new Stdin();
        $stdin->load();
        /** @var Command $command */
        $command = $reflectionClass->newInstanceArgs($args);
        $command->prepare();
        $stdin->arguments = $command->arguments;
        $stdin->options = $command->options;
        $stdin->load();
        return $command->execute($stdin, new Stdout());
    }

    public function getCommandNameFromServer(): string
    {
        return $_SERVER['argv'][0];
    }
}
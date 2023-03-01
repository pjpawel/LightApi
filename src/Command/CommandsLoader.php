<?php

namespace pjpawel\LightApi\Command;

use Exception;
use pjpawel\LightApi\Command\Input\Stdin;
use pjpawel\LightApi\Command\Output\Stdout;
use pjpawel\LightApi\Container\ClassDefinition;
use pjpawel\LightApi\Container\ContainerLoader;
use pjpawel\LightApi\Container\LazyServiceInterface;
use ReflectionClass;
use ReflectionNamedType;

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
     */
    public function runCommandFromName(string $commandName, ContainerLoader $container): int
    {
        $stdout = new Stdout();
        try {
            $className = $this->command[$commandName];
            $reflectionClass = new ReflectionClass($className);
            $constructor = $reflectionClass->getConstructor();
            if ($constructor !== null) {
                /** @var ClassDefinition $classDefinition */
                $classDefinition = $container->get($className);
                $args = $classDefinition->arguments;
                foreach ($constructor->getParameters() as $parameter) {
                    $parameterType = $parameter->getType();
                    if ($parameterType instanceof ReflectionNamedType) {
                        $args[] = $container->get($parameterType->getName());
                    }
                }
            } else {
                $args = [];
            }
            $stdin = new Stdin();
            /** @var Command $command */
            $command = $reflectionClass->newInstanceArgs($args);
            $command->prepare($stdin);
            /* Prepare input */
            $stdin->load();
            /* Inject services */
            if (is_subclass_of($command, LazyServiceInterface::class)) {
                $command->setContainer($container->prepareContainerBag($command::getAllServices()));
            }
            return $command->execute($stdin, $stdout);
        } catch (Exception $e) {
            $stdout->writeln([
                'Exception thrown during command',
                $e->getMessage(),
                'file: ' . $e->getFile(),
                'line: ' . $e->getLine()
            ]);
            return Command::FAILURE;
        }
    }

    public function getCommandNameFromServer(): string
    {
        return $_SERVER['argv'][0];
    }
}
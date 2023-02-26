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
     */
    public function runCommandFromName(string $commandName, ContainerLoader $container): int
    {
        $stdout = new Stdout();
        try {
            $className = $this->command[$commandName];
            $reflectionClass = new ReflectionClass($className);
            $constructor = $reflectionClass->getConstructor();
            $args = [];
            if ($constructor !== null) {
                foreach ($constructor->getParameters() as $parameter) {
                    $args[] = $container->get($parameter->getType()->getName());
                }
            }
            /** @var Command $command */
            $command = $reflectionClass->newInstanceArgs($args);
            $command->prepare();
            /* Prepare input */
            $stdin = new Stdin($command->arguments, $command->options);
            $stdin->load();
            return $command->execute($stdin, $stdout);
        } catch (\Exception $e) {
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
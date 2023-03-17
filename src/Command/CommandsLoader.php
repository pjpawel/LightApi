<?php

namespace pjpawel\LightApi\Command;

use Exception;
use pjpawel\LightApi\Command\Input\Stdin;
use pjpawel\LightApi\Command\Internal\CacheClearCommand;
use pjpawel\LightApi\Command\Internal\DebugCommandLoaderCommand;
use pjpawel\LightApi\Command\Internal\DebugContainerCommand;
use pjpawel\LightApi\Command\Internal\DebugCommand;
use pjpawel\LightApi\Command\Internal\DebugRouterCommand;
use pjpawel\LightApi\Command\Internal\KernelAwareCommand;
use pjpawel\LightApi\Command\Internal\WarmUpCacheCommand;
use pjpawel\LightApi\Command\Output\Stdout;
use pjpawel\LightApi\Container\Awareness\ContainerAwareInterface;
use pjpawel\LightApi\Container\ContainerLoader;
use pjpawel\LightApi\Container\Definition\ClassDefinition;
use pjpawel\LightApi\Kernel;
use ReflectionClass;
use ReflectionNamedType;

class CommandsLoader
{

    private const KERNEL_COMMANDS = [
        'kernel:debug' => DebugCommand::class,
        'kernel:cache:warmup' => WarmUpCacheCommand::class,
        'kernel:cache:clear' => CacheClearCommand::class,
        'kernel:debug:container' => DebugContainerCommand::class,
        'kernel:debug:router' => DebugRouterCommand::class,
        'kernel:debug:command' => DebugCommandLoaderCommand::class
    ];

    /**
     * @var array<string,string>
     */
    public array $command = [];

    
    public function __construct()
    {
        $this->command = self::KERNEL_COMMANDS;
    }

    public function registerCommand(string $commandName, string $className): void
    {
        $this->command[$commandName] = $className;
    }

    /**
     * @param string $commandName
     * @param ContainerLoader $container
     * @return int
     */
    public function runCommandFromName(string $commandName, ContainerLoader $container, ?Kernel $kernel = null): int
    {
        $stdout = new Stdout();
        try {
            $className = $this->command[$commandName];
            $reflectionClass = new ReflectionClass($className);
            $constructor = $reflectionClass->getConstructor();
            if ($constructor !== null) {
                if ($container->has($className)) {
                    /** @var ClassDefinition $classDefinition */
                    $classDefinition = $container->get($className);
                    $args = $classDefinition->arguments;
                } else {
                    $args = [];
                }
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
            if (is_subclass_of($command, ContainerAwareInterface::class)) {
                $command->setContainer($container);
            }
            /* If $command is KernelAwareCommand set Kernel */
            if (!is_null($kernel) && is_subclass_of($command, KernelAwareCommand::class)) {
                $command->setKernel($kernel);
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
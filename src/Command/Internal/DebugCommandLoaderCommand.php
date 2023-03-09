<?php

namespace pjpawel\LightApi\Command\Internal;

use pjpawel\LightApi\Command\CommandsLoader;
use pjpawel\LightApi\Command\Input\InputInterface;
use pjpawel\LightApi\Command\Output\OutputInterface;
use ReflectionClass;

class DebugCommandLoaderCommand extends KernelAwareCommand
{

    /**
     * @inheritDoc
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $reflectionKernel = new ReflectionClass($this->kernel);
        $commandLoaderReflection = $reflectionKernel->getProperty('commandLoader');
        /** @var CommandsLoader $commandLoader */
        $commandLoader = $commandLoaderReflection->getValue($this->kernel);

        $output->writeln(array_keys($commandLoader->command));

        return self::SUCCESS;
    }
}
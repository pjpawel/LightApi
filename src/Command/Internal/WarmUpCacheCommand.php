<?php

namespace pjpawel\LightApi\Command\Internal;

use pjpawel\LightApi\Command\CommandsLoader;
use pjpawel\LightApi\Command\Input\InputInterface;
use pjpawel\LightApi\Command\Output\OutputInterface;
use pjpawel\LightApi\Component\FilesManager;
use pjpawel\LightApi\Component\Serializer;
use pjpawel\LightApi\Container\ContainerLoader;
use pjpawel\LightApi\Kernel;
use pjpawel\LightApi\Route\Router;
use ReflectionClass;

class WarmUpCacheCommand extends KernelAwareCommand
{

    private FilesManager $filesManager;

    public function __construct()
    {
        $this->filesManager = new FilesManager();
    }

    /**
     * @inheritDoc
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $reflectionClass = new ReflectionClass(Kernel::class);
        /** @var Serializer $serializer */
        $serializer = $reflectionClass->getProperty('serializer')->getValue($this->kernel);
        $serializerClass = new ReflectionClass($serializer);
        $this->filesManager->removeDirRecursive($serializerClass->getProperty('serializedDir')->getValue($serializer));
        $serializer->makeSerialization([
            ContainerLoader::class => $reflectionClass->getProperty('containerLoader')->getValue($this->kernel),
            Router::class => $reflectionClass->getProperty('router')->getValue($this->kernel),
            CommandsLoader::class => $reflectionClass->getProperty('commandLoader')->getValue($this->kernel)
        ]);
        return self::SUCCESS;
    }
}
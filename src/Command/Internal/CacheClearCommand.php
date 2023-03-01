<?php

namespace pjpawel\LightApi\Command\Internal;

use pjpawel\LightApi\Command\Input\InputInterface;
use pjpawel\LightApi\Command\Output\OutputInterface;
use pjpawel\LightApi\Component\FilesManager;
use pjpawel\LightApi\Component\Serializer;
use pjpawel\LightApi\Kernel;
use ReflectionClass;

class CacheClearCommand extends KernelAwareCommand
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
        $serializerDir = $serializerClass->getProperty('serializedDir')->getValue($serializer);
        if (is_dir($serializerDir)) {
            $this->filesManager->removeDirRecursive($serializerDir);
        }
        return self::SUCCESS;
    }
}
<?php

namespace pjpawel\LightApi\Command\Internal;

use pjpawel\LightApi\Command\Input\InputInterface;
use pjpawel\LightApi\Command\Output\OutputInterface;
use pjpawel\LightApi\Component\Serializer;
use pjpawel\LightApi\Kernel;
use ReflectionClass;

class CacheClearCommand extends KernelAwareCommand
{

    /**
     * @inheritDoc
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $reflectionClass = new ReflectionClass(Kernel::class);
        /** @var Serializer $serializer */
        $serializer = $reflectionClass->getProperty('serializer')->getValue($this->kernel);
        $serializerClass = new ReflectionClass($serializer);
        if (rmdir($serializerClass->getProperty('serializedDir')->getValue($serializer))) {
            throw new \Exception('Cannot remove dir: ' .
                $serializerClass->getProperty('serializedDir')->getValue($serializer));
        }
        return self::SUCCESS;
    }
}
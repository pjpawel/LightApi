<?php

namespace pjpawel\LightApi\Component\Cache;

use pjpawel\LightApi\Command\Command;
use pjpawel\LightApi\Command\Input\InputInterface;
use pjpawel\LightApi\Command\Output\OutputInterface;
use pjpawel\LightApi\Container\LazyService\LazyServiceInterface;
use pjpawel\LightApi\Container\LazyService\LazyServiceTrait;
use pjpawel\LightApi\Kernel;
use Symfony\Component\Cache\Adapter\AbstractAdapter;

class SymfonyCacheClearCommand extends Command implements LazyServiceInterface
{

    use LazyServiceTrait;

    protected function kernelCache(): AbstractAdapter
    {
        return $this->container->get(Kernel::KERNEL_CACHE_NAME);
    }

//    public function prepare(InputInterface $input): void
//    {
//        $input->addArgument('pool');
//    }

    /**
     * @inheritDoc
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->kernelCache()->clear();
        return self::SUCCESS;
    }
}
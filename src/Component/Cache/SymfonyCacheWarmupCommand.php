<?php

namespace pjpawel\LightApi\Component\Cache;

use pjpawel\LightApi\Command\Command;
use pjpawel\LightApi\Command\Input\InputInterface;
use pjpawel\LightApi\Command\Output\OutputInterface;
use pjpawel\LightApi\Container\LazyService\LazyServiceInterface;
use pjpawel\LightApi\Container\LazyService\LazyServiceTrait;
use pjpawel\LightApi\Kernel;
use Symfony\Component\Cache\Adapter\AbstractAdapter;

class SymfonyCacheWarmupCommand extends Command implements LazyServiceInterface
{

    use LazyServiceTrait;

    protected function kernelCache(): AbstractAdapter
    {
        return $this->container->get(Kernel::KERNEL_CACHE_NAME);
    }

    /**
     * @inheritDoc
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->kernelCache()->reset();
        return self::SUCCESS;
    }
}
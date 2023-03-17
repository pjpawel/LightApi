<?php

namespace pjpawel\LightApi\Container\Awareness;

use Psr\Container\ContainerInterface;

trait ContainerAwareTrait
{
    protected ContainerInterface $container;

    /**
     * @inheritDoc
     */
    public function setContainer(ContainerInterface $container): void
    {
        $this->container = $container;
    }

}
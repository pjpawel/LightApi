<?php

namespace pjpawel\LightApi\Container\Awareness;

use Psr\Container\ContainerInterface;

interface ContainerAwareInterface
{
    /**
     * Method will be called after the construct to set container
     *
     * @param ContainerInterface $container
     * @return void
     */
    public function setContainer(ContainerInterface $container): void;
}
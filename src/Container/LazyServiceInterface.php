<?php

namespace pjpawel\LightApi\Container;

use Psr\Container\ContainerInterface;

interface LazyServiceInterface extends ContainerInterface
{

    public function setContainer(ContainerInterface $container);

    public function getAllServices();

}
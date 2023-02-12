<?php

namespace pjpawel\LightApi\Container;

use Psr\Container\ContainerInterface;

/**
 * Class to hold smaller container (inside class)
 */
class ContainerBag implements ContainerInterface
{

    use ContainerTrait;


}
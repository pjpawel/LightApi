<?php

namespace pjpawel\LightApi\Test\resources\classes;

use pjpawel\LightApi\Container\Awareness\ContainerAwareInterface;
use pjpawel\LightApi\Container\Awareness\ContainerAwareTrait;
use pjpawel\LightApi\Http\Response;
use pjpawel\LightApi\Route\AsRoute;

class ControllerTwo implements ContainerAwareInterface
{

    use ContainerAwareTrait;

    protected function logger(): Logger
    {
        return $this->container->get(Logger::class);
    }

    #[AsRoute('/index')]
    public function index(): Response
    {
        return new Response($this->logger()->tellOne());
    }

}
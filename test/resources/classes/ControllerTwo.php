<?php

namespace pjpawel\LightApi\Test\resources\classes;

use pjpawel\LightApi\Container\AsLazyService;
use pjpawel\LightApi\Container\LazyServiceInterface;
use pjpawel\LightApi\Container\LazyServiceTrait;
use pjpawel\LightApi\Http\Response;
use pjpawel\LightApi\Route\AsRoute;

class ControllerTwo implements LazyServiceInterface
{

    use LazyServiceTrait;

    #[AsLazyService]
    protected function logger(): Logger
    {
        return $this->container->get(__METHOD__);
    }

    #[AsRoute('/index')]
    public function index(): Response
    {
        return new Response($this->logger()->tellOne());
    }

}
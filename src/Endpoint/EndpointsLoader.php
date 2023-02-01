<?php

namespace pjpawel\LightApi\Endpoint;

class EndpointsLoader
{

    /**
     * @var string[]
     */
    private array $controllerPaths;
    /**
     * @var array<string, Endpoint>
     */
    public array $endpoints = [];

    /**
     * @param string[] $controllerPaths
     */
    public function __construct(array $controllerPaths)
    {
        $this->controllerPaths = $controllerPaths;
    }

    public function load(): void
    {
        foreach ($this->controllerPaths as $controllerPath) {
            //Here load controller
        }
    }

    /**
     * @param string $endpointPath
     * @return Endpoint
     */
    public function getPath(string $endpointPath): Endpoint
    {
        return $this->endpoints[$endpointPath];
    }

}
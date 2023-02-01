<?php

namespace pjpawel\LightApi\Runner;

use pjpawel\LightApi\Http\Request;
use pjpawel\LightApi\Kernel;

class HttpRunner implements RunnerInterface
{

    public Kernel $kernel;
    public Request $request;

    public function __construct(Kernel $kernel)
    {
        $this->kernel = $kernel;
        $this->request = Request::makeFromGlobals();
    }

    /**
     * @inheritDoc
     */
    public function run(): void
    {
        $response = $this->kernel->handleRequest($this->request);
        $response->send();
    }
}
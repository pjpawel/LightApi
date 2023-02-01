<?php

namespace pjpawel\LightApi\Debug;

use pjpawel\LightApi\Kernel;

class KernelDebugger
{

    public Kernel $kernel;

    public function __construct(Kernel $kernel)
    {
        $this->kernel = $kernel;
    }

    public function show(): string
    {

    }
}
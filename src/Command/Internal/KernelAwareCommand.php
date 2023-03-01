<?php

namespace pjpawel\LightApi\Command\Internal;

use pjpawel\LightApi\Command\Command;
use pjpawel\LightApi\Kernel;

abstract class KernelAwareCommand extends Command
{

    protected Kernel $kernel;

    public function setKernel(Kernel $kernel): void
    {
        $this->kernel = $kernel;
    }
}
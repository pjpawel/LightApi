<?php

namespace pjpawel\LightApi\Runner;

use pjpawel\LightApi\Kernel;

class CliRunner implements RunnerInterface
{

    private Kernel $kernel;
    public string $commandName;
    public int $result;

    public function __construct(Kernel $kernel, string $commandName)
    {
        $this->kernel = $kernel;
        $this->commandName = $commandName;
    }

    /**
     * @inheritDoc
     */
    public function run(): void
    {
        $this->result = $this->kernel->handleCommand($this->commandName);
    }
}
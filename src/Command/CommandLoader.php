<?php

namespace pjpawel\LightApi\Command;

class CommandLoader
{
    /**
     * @var string[]
     */
    private array $commandPaths;
    /**
     * @var array<string,string>[]
     */
    public array $command = [];

    /**
     * @param string[] $commandPaths
     */
    public function __construct(array $commandPaths)
    {
        $this->commandPaths = $commandPaths;
    }

    public function load(): void
    {
        foreach ($this->commandPaths as $commandPath) {
            $command = '';
            //Here load command names and paths
        }
    }

    public function getCommand(string $commandName): Command
    {
        return new $this->command[$commandName]();
    }

}
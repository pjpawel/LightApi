<?php

namespace pjpawel\LightApi\Command;

use pjpawel\LightApi\Command\Input\Argument;
use pjpawel\LightApi\Command\Input\InputInterface;
use pjpawel\LightApi\Command\Input\Option;
use pjpawel\LightApi\Command\Output\OutputInterface;
use pjpawel\LightApi\Exception\ProgrammerException;

class Command
{

    public const SUCCESS = 0;
    public const FAILURE = 1;
    public const INVALID = 2;

    /**
     * @var Option[]
     */
    public array $options = [];
    /**
     * @var Argument[]
     */
    public array $arguments = [];

    /**
     * Method to prepare command arguments
     *
     * @return void
     */
    public function prepare(): void
    {

    }

    /**
     * In this method you should be implement main logic of your command
     * In $input you will find arguments and options
     * In $output write to console
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @phpstan-ignore
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        throw new ProgrammerException('You must implement');
    }

    public function addOption(
        string $name,
        string $shortcut,
        int $type = Option::OPTIONAL,
        mixed $default = null,
        string $description = null
    ): void
    {
        $this->options[] = new Option($name, $shortcut, $type, $default, $description);
    }

    public function addArgument(
        string $name,
        int $type = Argument::REQUIRED,
        string $description = null
    ): void
    {
        $this->arguments[] = new Argument($name, $type, $description);
    }
}
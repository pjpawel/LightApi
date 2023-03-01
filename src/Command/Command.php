<?php

namespace pjpawel\LightApi\Command;

use pjpawel\LightApi\Command\Input\Argument;
use pjpawel\LightApi\Command\Input\InputInterface;
use pjpawel\LightApi\Command\Input\Option;
use pjpawel\LightApi\Command\Output\OutputInterface;

abstract class Command
{

    public const SUCCESS = 0;
    public const FAILURE = 1;
    public const INVALID = 2;

    /**
     * Method to prepare command arguments
     *
     * @param InputInterface $input
     * @return void
     */
    public function prepare(/** @scrutinizer ignore-unused */ InputInterface $input): void
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
     */
    abstract public function execute(InputInterface $input, OutputInterface $output): int;
}
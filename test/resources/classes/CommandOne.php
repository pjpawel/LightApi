<?php

namespace pjpawel\LightApi\Test\resources\classes;

use pjpawel\LightApi\Command\AsCommand;
use pjpawel\LightApi\Command\Command;
use pjpawel\LightApi\Command\Input\InputInterface;
use pjpawel\LightApi\Command\Output\OutputInterface;

#[AsCommand('command:one')]
class CommandOne extends Command
{

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->write('CommandOne is running');
        return self::SUCCESS;
    }
}
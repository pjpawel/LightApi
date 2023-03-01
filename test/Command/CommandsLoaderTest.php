<?php

namespace pjpawel\LightApi\Test\Command;

use pjpawel\LightApi\Command\CommandsLoader;
use PHPUnit\Framework\TestCase;
use pjpawel\LightApi\Container\ContainerLoader;
use pjpawel\LightApi\Test\resources\classes\CommandOne;

/**
 * @covers \pjpawel\LightApi\Command\CommandsLoaders
 */
class CommandsLoaderTest extends TestCase
{

    /**
     * @covers \pjpawel\LightApi\Command\CommandsLoader::runCommandFromName
     */
    public function testRunCommandFromName()
    {
        $commandsLoader = new CommandsLoader();
        $commandsLoader->registerCommand('command:one', CommandOne::class);
        $this->expectOutputString('CommandOne is running');
        $result = $commandsLoader->runCommandFromName('command:one', new ContainerLoader());
        $this->assertEquals(0, $result);
    }

    /**
     * @covers \pjpawel\LightApi\Command\CommandsLoader::getCommandNameFromServer
     */
    public function testGetCommandNameFromServer()
    {
        $commandsLoader = new CommandsLoader();
        $this->assertEquals($_SERVER['argv'][0], $commandsLoader->getCommandNameFromServer());
    }

    /**
     * @covers \pjpawel\LightApi\Command\CommandsLoader::registerCommand
     */
    public function testRegisterCommand()
    {
        $commandsLoader = new CommandsLoader();
        $this->assertCount(1, $commandsLoader->command);
        $commandsLoader->registerCommand('command:one', CommandOne::class);
        $this->assertCount(2, $commandsLoader->command);
    }
}

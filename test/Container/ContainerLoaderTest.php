<?php

namespace pjpawel\LightApi\Test\Container;

use pjpawel\LightApi\Components\Logger\SimpleLogger\SimpleLogger;
use pjpawel\LightApi\Container\AliasDefinition;
use pjpawel\LightApi\Container\ClassDefinition;
use pjpawel\LightApi\Container\ContainerLoader;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @covers \pjpawel\LightApi\Container\ContainerLoader
 */
class ContainerLoaderTest extends TestCase
{

    public const CONFIG = [
        SimpleLogger::class => [
            __DIR__ . '/../../tmp/test.log'
        ],
        LoggerInterface::class => '@' . SimpleLogger::class
    ];

    private function assertLoggerIsSimpleLogger($logger): void
    {
        $this->assertTrue(is_subclass_of($logger, LoggerInterface::class));
        $this->assertTrue(is_a($logger, SimpleLogger::class));
    }

    /**
     * @covers \pjpawel\LightApi\Container\ContainerLoader::createDefinitions
     */
    public function test__construct(): void
    {
        $container = new ContainerLoader(self::CONFIG);
        $logger = $container->get(SimpleLogger::class);
        $this->assertLoggerIsSimpleLogger($logger);
        $logger = $container->get(LoggerInterface::class);
        $this->assertLoggerIsSimpleLogger($logger);
    }

    /**
     * @covers \pjpawel\LightApi\Container\ContainerLoader::add
     */
    public function testAdd(): void
    {
        $config = [
            'name' => SimpleLogger::class,
            'args' => [
                __DIR__ . '/../../tmp/test.log'
            ]
        ];
        $container = new ContainerLoader([]);
        $container->add($config);
        $this->assertLoggerIsSimpleLogger($container->get(SimpleLogger::class));
    }

    /**
     * @covers \pjpawel\LightApi\Container\ContainerLoader::addDefinitions
     */
    public function testAddDefinitions(): void
    {
        $definitions = (new ContainerLoader(self::CONFIG))->definitions;
        $container = new ContainerLoader([]);
        $container->addDefinitions($definitions);
        $this->assertLoggerIsSimpleLogger($container->get(SimpleLogger::class));
        $this->assertLoggerIsSimpleLogger($container->get(LoggerInterface::class));
    }

    /**
     * @covers \pjpawel\LightApi\Container\ContainerLoader::getDefinitions
     */
    public function testGetDefinitions(): void
    {
        $container = new ContainerLoader(self::CONFIG);
        $definitions = $container->getDefinitions([
            SimpleLogger::class,
            LoggerInterface::class
        ]);
        $this->assertTrue(is_a($definitions[0], ClassDefinition::class));
        $this->assertTrue(is_a($definitions[1], AliasDefinition::class));
    }
}

<?php

namespace pjpawel\LightApi\Test;

use PHPUnit\Framework\TestCase;
use pjpawel\LightApi\Component\Env;
use pjpawel\LightApi\Container\ContainerLoader;
use pjpawel\LightApi\Kernel;
use pjpawel\LightApi\Test\resources\classes\Logger;
use ReflectionClass;

/**
 * @covers \pjpawel\LightApi\Kernel
 */
class KernelTest extends TestCase
{

    private function createKernel(): Kernel
    {
        $configDir = __DIR__ . '/resources/config/base_config';
        //$config = Env::getConfigFromEnv($configDir);
        return new Kernel($configDir);
    }

    /**
     * @covers \pjpawel\LightApi\Kernel::__construct
     */
    public function test__construct(): void
    {
        $kernel = $this->createKernel();
        $this->assertTrue(is_object($kernel));
    }

    /**
     * @covers \pjpawel\LightApi\Kernel::boot
     */
    public function testBoot(): void
    {
        $kernel = $this->createKernel();
        $reflectionClass = new ReflectionClass(Kernel::class);
        /** @var ContainerLoader $container */
        $container = $reflectionClass->getProperty('containerLoader')->getValue($kernel);
        $this->assertTrue($container->has(Logger::class));
        /** @var Logger $logger */
        $logger = $container->get(Logger::class);
        $this->assertTrue($logger instanceof Logger);
        $this->assertEquals('tellOne', $logger->tellOne());
    }
}

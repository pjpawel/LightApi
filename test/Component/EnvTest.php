<?php

namespace pjpawel\LightApi\Test\Component;

use PHPUnit\Framework\TestCase;
use pjpawel\LightApi\Component\Env;
use pjpawel\LightApi\Test\resources;

/**
 * @covers \pjpawel\LightApi\Component\Env
 */
class EnvTest extends TestCase
{

    /**
     * @covers \pjpawel\LightApi\Component\Env::getConfigFromEnv
     */
    public function testGetConfigFromEnv()
    {
        $env = new Env();
        $dir = __DIR__ . '/../resources/config/base_config/';
        $config = $env->getConfigFromEnv($dir);
        $this->assertEquals(
            [
                'projectDir' => realpath(__DIR__ . '/../../'),
                'env'=>'test',
                'debug' => true,
                'trustedIPs' => [],
                'extensions' => [
                ],
                'container' => [
                    resources\classes\Logger::class => []
                ],
                'services' => realpath(__DIR__ . '/../resources/classes/')
            ],
            $config);

        $dir = __DIR__ . '/../resources/config/config_with_local/';
        $config = $env->getConfigFromEnv($dir);
        $this->assertEquals(
            [
                'env'=>'test',
                'debug' => true,
                'trustedIPs' => ['127.0.0.1'],
                'extensions' => [
                ],
                'container' => [
                    resources\classes\Logger::class => []
                ],
                'services' => realpath(__DIR__ . '/../resources/classes/')
            ],
            $config);
    }

    /**
     * @covers \pjpawel\LightApi\Component\Env::loadConfigFile
     */
    public function testLoadConfigFile()
    {
        $env = new Env();
        $filename = __DIR__ . '/../resources/config/base_config/env.php';
        $config = $env->loadConfigFile($filename);
        $this->assertIsArray($config);
        $this->assertEquals(['env'=>'test', 'debug' => true], $config);
    }
}

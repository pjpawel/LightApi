<?php

namespace pjpawel\LightApi\Test;

use PHPUnit\Framework\TestCase;
use pjpawel\LightApi\Env;

/**
 * @covers \pjpawel\LightApi\Env
 */
class EnvTest extends TestCase
{

    /**
     * @covers \pjpawel\LightApi\Env::getConfigFromEnv
     */
    public function testGetConfigFromEnv()
    {
        $dir = __DIR__ . '/resources/config/base_config/';
        $config = Env::getConfigFromEnv($dir);
        $this->assertEquals(
            [
                'projectDir' => realpath(__DIR__ . '/../'),
                'env'=>'test',
                'debug' => true,
                'trustedIPs' => [],
                'extensions' => [
                ],
                'container' => [
                    resources\classes\Logger::class => []
                ],
                'services' => realpath(__DIR__ . '/resources/classes/')
            ],
            $config);

        $dir = __DIR__ . '/resources/config/config_with_local/';
        $config = Env::getConfigFromEnv($dir);
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
                'services' => realpath(__DIR__ . '/resources/classes/')
            ],
            $config);
    }

    /**
     * @covers \pjpawel\LightApi\Env::loadConfigFile
     */
    public function testLoadConfigFile()
    {
        $filename = __DIR__ . '/resources/config/base_config/env.php';
        $config = Env::loadConfigFile($filename);
        $this->assertIsArray($config);
        $this->assertEquals(['env'=>'test', 'debug' => true], $config);
    }
}

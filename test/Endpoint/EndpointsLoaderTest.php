<?php

namespace pjpawel\LightApi\Test\Endpoint;

use pjpawel\LightApi\Endpoint\Endpoint;
use pjpawel\LightApi\Endpoint\EndpointsLoader;
use PHPUnit\Framework\TestCase;
use pjpawel\LightApi\Http\Request;
use pjpawel\LightApi\Test\resources\classes\ControllerOne;

/**
 * @covers \pjpawel\LightApi\Endpoint\EndpointsLoader
 */
class EndpointsLoaderTest extends TestCase
{

    public const ENDPOINT_CONFIG = [
        'resources/classes' => [
            'pjpawel\\LightApi\\Test\\resources\\classes\\'
        ]
    ];

    public const TEST_DIR = __DIR__ . '/../../test';

    /**
     * @covers \pjpawel\LightApi\Endpoint\EndpointsLoader
     */
    public function test__construct(): void
    {
        $loader = new EndpointsLoader(self::ENDPOINT_CONFIG);
        $this->assertTrue($loader instanceof EndpointsLoader);
    }

    /**
     * @covers \pjpawel\LightApi\Endpoint\EndpointsLoader::load
     */
    public function testLoad(): void
    {
        $loader = new EndpointsLoader(self::ENDPOINT_CONFIG);
        $loader->load(self::TEST_DIR);
        $this->assertTrue($loader->loaded);
        $this->assertNotEmpty($loader->endpoints);
    }

    /**
     * @covers \pjpawel\LightApi\Endpoint\EndpointsLoader::loadEndpointClass
     */
    public function testLoadEndpointClass(): void
    {
        $loader = new EndpointsLoader(self::ENDPOINT_CONFIG);

        $reflectionClass = new \ReflectionClass(EndpointsLoader::class);
        $method = $reflectionClass->getMethod('loadEndpointClass');
        $method->invoke($loader, ControllerOne::class);
        $this->assertNotEmpty($loader->endpoints);
        $this->assertCount(5, $loader->endpoints);
    }

    /**
     * @covers \pjpawel\LightApi\Endpoint\EndpointsLoader::getEndpoint
     */
    public function testGetEndpoint(): void
    {
        $loader = new EndpointsLoader(self::ENDPOINT_CONFIG);
        $loader->load(self::TEST_DIR);
        $request = new Request([], [], [], [], ['REQUESTED_METHOD' => 'GET', 'REQUEST_URI' => '/index', 'REMOTE_ADDR' => '127.0.0.1']);
        $endpoint = $loader->getEndpoint($request);
        $this->assertTrue($endpoint instanceof Endpoint);
    }
}

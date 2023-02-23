<?php

namespace pjpawel\LightApi\Test\Endpoint;

use Exception;
use pjpawel\LightApi\Endpoint\Endpoint;
use pjpawel\LightApi\Endpoint\EndpointsLoader;
use PHPUnit\Framework\TestCase;
use pjpawel\LightApi\Http\Exception\MethodNotAllowedHttpException;
use pjpawel\LightApi\Http\Request;
use pjpawel\LightApi\Http\ResponseStatus;
use pjpawel\LightApi\Test\resources\classes\ControllerOne;

/**
 * @covers \pjpawel\LightApi\Endpoint\EndpointsLoader
 */
class EndpointsLoaderTest extends TestCase
{

    /**
     * @covers \pjpawel\LightApi\Endpoint\EndpointsLoader::getErrorResponse
     */
    public function testGetErrorResponse(): void
    {
        $loader = new EndpointsLoader();
        $exception = new Exception('Something wrong happened');
        $response = $loader->getErrorResponse($exception);
        $this->assertEquals('Internal server error occurred', $response->content);
        $this->assertEquals(ResponseStatus::INTERNAL_SERVER_ERROR, $response->status);
        $exception = new MethodNotAllowedHttpException('Something wrong happened');
        $response = $loader->getErrorResponse($exception);
        $this->assertEquals('Something wrong happened', $response->content);
        $this->assertEquals(ResponseStatus::METHOD_NOT_ALLOWED, $response->status);
    }

    /**
     * @covers \pjpawel\LightApi\Endpoint\EndpointsLoader::getEndpoint
     * @covers \pjpawel\LightApi\Endpoint\EndpointsLoader::registerEndpoint
     */
    public function testGetEndpoint(): void
    {
        $loader = new EndpointsLoader();
        $loader->registerEndpoint(ControllerOne::class, 'index', '/index', []);
        $request = new Request([], [], [], [], ['REQUESTED_METHOD' => 'GET', 'REQUEST_URI' => '/index', 'REMOTE_ADDR' => '127.0.0.1']);
        $endpoint = $loader->getEndpoint($request);
        $this->assertTrue($endpoint instanceof Endpoint);
    }
}

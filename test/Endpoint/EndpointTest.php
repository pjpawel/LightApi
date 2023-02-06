<?php

namespace pjpawel\LightApi\Test\Endpoint;

use pjpawel\LightApi\Container\ContainerLoader;
use pjpawel\LightApi\Endpoint\Endpoint;
use PHPUnit\Framework\TestCase;
use pjpawel\LightApi\Http\Request;
use pjpawel\LightApi\Test\resources\classes\ControllerOne;
use pjpawel\LightApi\Test\resources\classes\Logger;

/**
 * @covers \pjpawel\LightApi\Endpoint\Endpoint
 */
class EndpointTest extends TestCase
{

    /**
     * @covers \pjpawel\LightApi\Endpoint\Endpoint
     */
    public function test__construct(): void
    {
        $endpoint = new Endpoint(ControllerOne::class, 'index', '/index', []);
        $this->assertEquals('/^\/index/', $endpoint->regexPath);
        $this->assertEquals('/index', $endpoint->path);
        $this->assertEquals([], $endpoint->httpMethods);
    }

    /**
     * @covers \pjpawel\LightApi\Endpoint\Endpoint
     */
    public function test__constructWithStringParam(): void
    {
        $endpoint = new Endpoint(ControllerOne::class, 'echo', '/echo/{identifier}', ['POST']);
        $this->assertEquals('/^\/echo\/(\w+?)/', $endpoint->regexPath);
        $this->assertEquals('/echo/{identifier}', $endpoint->path);
        $this->assertEquals(['POST'], $endpoint->httpMethods);
    }

    /**
     * @covers \pjpawel\LightApi\Endpoint\Endpoint
     */
    public function test__constructWithIntParam(): void
    {
        $endpoint = new Endpoint(ControllerOne::class, 'echoInt', '/echo/{identifierInt}', ['GET']);
        $this->assertEquals('/^\/echo\/(\d+?)/', $endpoint->regexPath);
        $this->assertEquals('/echo/{identifierInt}', $endpoint->path);
        $this->assertEquals(['GET'], $endpoint->httpMethods);
    }

    /**
     * @covers \pjpawel\LightApi\Endpoint\Endpoint
     */
    public function test__constructWithTwoParams(): void
    {
        $endpoint = new Endpoint(ControllerOne::class, 'echoTwoParams', '/echo/{channel}/list/{identifier}', ['POST', 'PUT']);
        $this->assertEquals('/^\/echo\/(\w+?)\/list\/(\d+?)/', $endpoint->regexPath);
        $this->assertEquals('/echo/{channel}/list/{identifier}', $endpoint->path);
        $this->assertEquals(['POST', 'PUT'], $endpoint->httpMethods);
    }

    /**
     * @covers \pjpawel\LightApi\Endpoint\Endpoint::execute
     */
    public function testExecute(): void
    {
        $endpoint = new Endpoint(ControllerOne::class, 'echoInt', '/echo/{identifierInt}', ['GET']);
        $container = new ContainerLoader([Logger::class => []]);
        $request = new Request([], [], [], [], ['REQUESTED_METHOD' => 'GET', 'REQUEST_URI' => '/echo/12', 'REMOTE_ADDR' => '127.0.0.1']);
        $response = $endpoint->execute($container, $request);
        $this->assertEquals('echo12', $response->content);
    }

    /**
     * @covers \pjpawel\LightApi\Endpoint\Endpoint::execute
     */
    public function testExecuteWithTwoParams(): void
    {
        $endpoint = new Endpoint(ControllerOne::class, 'echoTwoParams', '/echo/{channel}/list/{identifier}', ['POST', 'PUT']);
        $container = new ContainerLoader([Logger::class => []]);
        $request = new Request([], [], [], [], ['REQUESTED_METHOD' => 'GET', 'REQUEST_URI' => '/echo/volvo/list/15', 'REMOTE_ADDR' => '127.0.0.1']);
        $response = $endpoint->execute($container, $request);
        $this->assertEquals('echo:volvo:15', $response->content);
    }

    /**
     * @covers \pjpawel\LightApi\Endpoint\Endpoint::execute
     */
    public function testExecuteWithQuery(): void
    {
        $endpoint = new Endpoint(ControllerOne::class, 'echoQuery', '/echo/{identifier}', ['GET']);
        $container = new ContainerLoader([Logger::class => []]);
        $request = new Request(['id' => 19], [], [], [], ['REQUESTED_METHOD' => 'GET', 'REQUEST_URI' => '/echo/abc', 'REMOTE_ADDR' => '127.0.0.1']);
        $response = $endpoint->execute($container, $request);
        $this->assertEquals('requestId:19', $response->content);
    }


}

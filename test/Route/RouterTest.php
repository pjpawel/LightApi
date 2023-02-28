<?php

namespace pjpawel\LightApi\Test\Route;

use Exception;
use pjpawel\LightApi\Route\Route;
use pjpawel\LightApi\Route\Router;
use PHPUnit\Framework\TestCase;
use pjpawel\LightApi\Http\Exception\MethodNotAllowedHttpException;
use pjpawel\LightApi\Http\Request;
use pjpawel\LightApi\Http\ResponseStatus;
use pjpawel\LightApi\Test\resources\classes\ControllerOne;

/**
 * @covers \pjpawel\LightApi\Route\Router
 */
class RouterTest extends TestCase
{

    /**
     * @covers \pjpawel\LightApi\Route\Router::getErrorResponse
     */
    public function testGetErrorResponse(): void
    {
        $router = new Router();
        $exception = new Exception('Something wrong happened');
        $response = $router->getErrorResponse($exception);
        $this->assertEquals('Internal server error occurred', $response->content);
        $this->assertEquals(ResponseStatus::INTERNAL_SERVER_ERROR, $response->status);
        $exception = new MethodNotAllowedHttpException('Something wrong happened');
        $response = $router->getErrorResponse($exception);
        $this->assertEquals('Something wrong happened', $response->content);
        $this->assertEquals(ResponseStatus::METHOD_NOT_ALLOWED, $response->status);
    }

    /**
     * @covers \pjpawel\LightApi\Route\Router::getRoute
     * @covers \pjpawel\LightApi\Route\Router::registerRoute
     */
    public function testGetRoute(): void
    {
        $loader = new Router();
        $loader->registerRoute(ControllerOne::class, 'index', '/index', []);
        $request = new Request([], [], [], [], ['REQUESTED_METHOD' => 'GET', 'REQUEST_URI' => '/index', 'REMOTE_ADDR' => '127.0.0.1']);
        $route = $loader->getRoute($request);
        $this->assertTrue($route instanceof Route);
    }
}

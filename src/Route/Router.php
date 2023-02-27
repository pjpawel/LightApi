<?php

namespace pjpawel\LightApi\Route;

use Exception;
use pjpawel\LightApi\Http\Exception\HttpException;
use pjpawel\LightApi\Http\Exception\MethodNotAllowedHttpException;
use pjpawel\LightApi\Http\Exception\NotFoundHttpException;
use pjpawel\LightApi\Http\Request;
use pjpawel\LightApi\Http\Response;
use pjpawel\LightApi\Http\ResponseStatus;

class Router
{

    /**
     * @var Route[]
     */
    public array $routes = [];

    /**
     * Register new endpoint
     *
     * @param string $className
     * @param string $methodName
     * @param string $path
     * @param array $httpMethods
     * @return void
     * @throws \ReflectionException
     */
    public function registerEndpoint(string $className, string $methodName, string $path, array $httpMethods): void
    {
        $route = new Route($className, $methodName, $path, $httpMethods);
        $route->makeRegexPath();
        $this->routes[] = $route;
    }

    /**
     * @param Request $request
     * @return Route
     * @throws NotFoundHttpException
     * @throws MethodNotAllowedHttpException
     */
    public function getRoute(Request $request): Route
    {
        $methodNotAllowed = false;
        foreach ($this->routes as $route) {
            if (preg_match($route->regexPath, $request->path) === 1) {
                if (!empty($route->httpMethods) && !in_array($request->method, $route->httpMethods)) {
                    $methodNotAllowed = true;
                } else {
                    $matchedRoute = $route;
                }
                break;
            }
        }
        if (!isset($matchedRoute)) {
            throw $methodNotAllowed ? new MethodNotAllowedHttpException() : new NotFoundHttpException();
        }
        return $matchedRoute;
    }

    public function getErrorResponse(Exception|HttpException $exception): Response
    {
        if ($exception instanceof HttpException) {
            return new Response($exception->getMessage(), ResponseStatus::from($exception->getCode()));
        } else {
            return new Response('Internal server error occurred', ResponseStatus::INTERNAL_SERVER_ERROR);
        }
    }
}
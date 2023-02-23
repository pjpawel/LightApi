<?php

namespace pjpawel\LightApi\Endpoint;

use Exception;
use pjpawel\LightApi\Http\Exception\HttpException;
use pjpawel\LightApi\Http\Exception\MethodNotAllowedHttpException;
use pjpawel\LightApi\Http\Exception\NotFoundHttpException;
use pjpawel\LightApi\Http\Request;
use pjpawel\LightApi\Http\Response;
use pjpawel\LightApi\Http\ResponseStatus;

class EndpointsLoader
{

    /**
     * @var Endpoint[]
     */
    public array $endpoints = [];

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
        $endpoint = new Endpoint($className, $methodName, $path, $httpMethods);
        $endpoint->makeRegexPath();
        $this->endpoints[] = $endpoint;
    }

    /**
     * @param Request $request
     * @return Endpoint
     * @throws NotFoundHttpException
     * @throws MethodNotAllowedHttpException
     */
    public function getEndpoint(Request $request): Endpoint
    {
        $methodNotAllowed = false;
        foreach ($this->endpoints as $endpoint) {
            if (preg_match($endpoint->regexPath, $request->path) === 1) {
                if (!empty($endpoint->httpMethods) && !in_array($request->method, $endpoint->httpMethods)) {
                    $methodNotAllowed = true;
                } else {
                    $matchedEndpoint = $endpoint;
                }
                break;
            }
        }
        if (!isset($matchedEndpoint)) {
            throw $methodNotAllowed ? new MethodNotAllowedHttpException() : new NotFoundHttpException();
        }
        return $matchedEndpoint;
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
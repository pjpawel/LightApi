<?php

namespace pjpawel\LightApi\Endpoint;

use Exception;
use pjpawel\LightApi\Http\Exception\HttpException;
use pjpawel\LightApi\Http\Exception\MethodNotAllowedHttpException;
use pjpawel\LightApi\Http\Exception\NotFoundHttpException;
use pjpawel\LightApi\Http\Request;
use pjpawel\LightApi\Http\Response;
use pjpawel\LightApi\Http\ResponseStatus;
use pjpawel\LightApi\Kernel\ProgrammerException;
use ReflectionClass;

class EndpointsLoader
{

    private array $controllerPaths;
    /**
     * @var Endpoint[]
     */
    public array $endpoints = [];
    public bool $loaded = false;

    /**
     * @param array $controllerPaths
     */
    public function __construct(array $controllerPaths)
    {
        $this->controllerPaths = $controllerPaths;
    }

    /**
     * @param string $projectDir
     * @return void
     * @throws \ReflectionException
     * @throws ProgrammerException
     */
    public function load(string $projectDir): void
    {
        $classFinder = new ClassFinder();
        foreach ($this->controllerPaths as $controllerPath => $controllerNamespaces) {
            if (!is_array($controllerNamespaces)) {
                throw new ProgrammerException('ControllerNamespaces should be in array');
            }
            $controllerPath = $projectDir . DIRECTORY_SEPARATOR . $controllerPath;
            if (is_dir($controllerPath)) {
                $allClasses = $classFinder->getAllClassInDir($controllerPath);
                foreach ($controllerNamespaces as $controllerNamespace) {
                    $classes = $classFinder->getAllClassFromNamespace($controllerNamespace, $allClasses);
                    foreach ($classes as $class) {
                        $this->loadEndpointClass($class);
                    }
                }
            } else {
                throw new ProgrammerException('Invalid controller path');
            }
        }
        $this->loaded = true;
    }

    /**
     * @param string $class
     * @return void
     * @throws \ReflectionException
     */
    private function loadEndpointClass(string $class): void
    {
        $reflectionClass = new ReflectionClass($class);
        $methods = $reflectionClass->getMethods();
        foreach ($methods as $method) {
            $attributes = $method->getAttributes(AsRoute::class);
            if (!empty($attributes)) {
                $attribute = $attributes[0];
                $arguments = $attribute->getArguments();
                $endpoint = new Endpoint($class, $method->getName(), $arguments[0], $arguments[1] ?? []);
                $endpoint->makeRegexPath();
                $this->endpoints[] = $endpoint;
            }
        }
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

    public function serialize(): array
    {
        $data = [];
        foreach ($this->endpoints as $endpoint) {
            $data[] = $endpoint->serialize();
        }
        return $data;
    }

    public static function unserialize(array $config): self
    {
        $self = new self([]);
        foreach ($config as $endpointData) {
            $self->endpoints[] = Endpoint::unserialize($endpointData);
        }
        $self->loaded = true;
        return $self;
    }
}
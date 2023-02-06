<?php

namespace pjpawel\LightApi\Endpoint;

use pjpawel\LightApi\Http\Exception\MethodNotAllowedException;
use pjpawel\LightApi\Http\Exception\NotFoundHttpException;
use pjpawel\LightApi\Http\Request;
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
     */
    public function load(string $projectDir): void
    {
        $classFinder = new ClassFinder();
        foreach ($this->controllerPaths as $controllerPath => $controllerNamespaces) {
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
                throw new \Exception('Invalid controller path');
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
            $attributes = $method->getAttributes(Route::class);
            if (!empty($attributes)) {
                $attribute = $attributes[0];
                $arguments = $attribute->getArguments();
                $this->endpoints[] = new Endpoint($class, $method, $arguments[0], $arguments[1] ?? []);
            }
        }
    }

    /**
     * @param Request $request
     * @return Endpoint
     * @throws NotFoundHttpException
     * @throws MethodNotAllowedException
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
            throw $methodNotAllowed ? new MethodNotAllowedException() : new NotFoundHttpException();
        }
        return $matchedEndpoint;
    }

}
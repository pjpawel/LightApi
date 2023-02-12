<?php

namespace pjpawel\LightApi\Container;

use Psr\Container\ContainerInterface;

trait LazyServiceTrait
{

    public ?ContainerInterface $container;

    public function setContainer(?ContainerInterface $container): void
    {
        if (isset($this->container)) {
            $this->container = $container;
        }
    }

    public function getLazyServices(): array
    {
        /** @var array<string,string> $services */
        $services = [];
        if (method_exists(get_parent_class(self::class), __FUNCTION__)) {
            $services = parent::getLazyServices();
        }
        $reflectionClass = new \ReflectionClass(static::class);
        foreach ($reflectionClass->getMethods() as $method) {
            $reflectionAttributes = $method->getAttributes(AsLazyService::class);
            if (!isset($reflectionAttributes[0])) {
                continue;
            }
            $returnType = $method->getReturnType();
            if (!$returnType instanceof \ReflectionNamedType || $returnType->isBuiltin()) {
                continue;
            }
            $services[self::class . '::' . $method->getName()] = $returnType->getName();
        }


        return $services;
    }

}
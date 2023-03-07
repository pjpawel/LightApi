<?php

namespace pjpawel\LightApi\Container\LazyService;

use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionNamedType;

trait LazyServiceTrait
{

    public ContainerInterface $container;

    /**
     * @inheritDoc
     */
    public function setContainer(ContainerInterface $container): void
    {
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    public static function getAllServices(): array
    {
        /** @var array<string,string> $services */
        $services = [];
        if (get_parent_class(self::class) &&
            method_exists(get_parent_class(self::class), __FUNCTION__)) {
            $services = parent::getLazyServices();
        }
        $reflectionClass = new ReflectionClass(self::class);
        foreach ($reflectionClass->getMethods() as $method) {
            $reflectionAttributes = $method->getAttributes(AsLazyService::class);
            if (!isset($reflectionAttributes[0])) {
                continue;
            }
            // Allow to set in AsLazyService is of service
            $returnType = $method->getReturnType();
            if (!$returnType instanceof ReflectionNamedType || $returnType->isBuiltin()) {
                throw new \Exception(sprintf('%s must return class', $method->getName()));
            }
            $services[self::class . '::' . $method->getName()] = $returnType->getName();
        }
        return $services;
    }

}
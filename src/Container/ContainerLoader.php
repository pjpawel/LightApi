<?php

namespace pjpawel\LightApi\Container;

use Psr\Container\ContainerInterface;
use pjpawel\LightApi\Container\ContainerNotFoundException;
use ReflectionClass;

class ContainerLoader implements ContainerInterface
{

    /**
     * @var array<string, Definition>
     */
    public array $definitions;

    /**
     * @param array<string, string|array> $config
     * @throws \Exception
     */
    public function __construct(array $config)
    {
        $this->createDefinitions($config);
    }

    /**
     * @param array<string, string|array> $config
     * @throws \Exception
     */
    private function createDefinitions(array $config): void
    {
        foreach ($config as $name => $value) {
            if (str_starts_with($name, '@')) {
                $this->definitions[$name] = new AliasDefinition($name, $value);
            } elseif (class_exists($name)) {
                $this->definitions[$name] = new ClassDefinition($name, $value);
            } elseif (interface_exists($name)) {
                $this->definitions[$name] = new InterfaceDefinition($name, $value);
            } else {
                throw new \Exception('Invalid container definition for name ' . $name);
            }
        }
        $this->definitions['container'] = new ClassDefinition('container', []);
        $this->definitions['container']->object = $this;
        $this->definitions['containerInterface'] = new InterfaceDefinition('containerInterface', '@container');
    }

    /**
     * You must use name and args. Optionally you can provide object
     *
     * @param array $definition
     */
    public function add(array $definition): void
    {
        $definition = new ClassDefinition($definition['name'], $definition['args']);
        $definition->object = $definition['object'] ?? null;
        $this->definitions[$definition['class']] = $definition;
    }

    /**
     * @param string $id
     * @return object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \ReflectionException
     * @throws \pjpawel\LightApi\Container\ContainerNotFoundException
     */
    public function get(string $id): object
    {
        if (!isset($this->definitions[$id])) {
            throw new ContainerNotFoundException();
        }
        if ($this->definitions[$id]->object === null) {
            $this->loadObject($id);
        }
        return $this->definitions[$id]->object;
    }

    /**
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool
    {
        if (!isset($this->definitions[$id])) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @param string $id
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    protected function loadObject(string $id): void
    {
        $definition = $this->definitions[$id];
        if ($definition instanceof ClassDefinition) {
            $this->definitions[$id]->object = (new ReflectionClass($definition->name))
                ->newInstanceArgs($definition->arguments);
        } else {
            /** @var $definition InDirectDefinition */
            $this->definitions[$id]->object = $this->get($definition->className);
        }
    }


}
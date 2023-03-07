<?php

namespace pjpawel\LightApi\Container;

use pjpawel\LightApi\Container\Definition\AliasDefinition;
use pjpawel\LightApi\Container\Definition\ClassDefinition;
use pjpawel\LightApi\Container\Definition\Definition;
use pjpawel\LightApi\Container\Definition\InterfaceDefinition;
use pjpawel\LightApi\Exception\ProgrammerException;
use Psr\Container\ContainerInterface;

class ContainerLoader implements ContainerInterface
{

    use ContainerTrait;

    /**
     * @var array<string,Definition>
     */
    public array $definitions;

    /**
     * @param array<string, string|array> $config
     * @throws \Exception
     */
    public function createDefinitionsFromConfig(array $config): void
    {
        foreach ($config as $name => $value) {
            if (is_string($value) && str_starts_with($value, '@')) {
                $this->definitions[$name] = new AliasDefinition($name, substr($value, 1));
            } elseif (class_exists($name)) {
                $this->definitions[$name] = new ClassDefinition($name, $value);
            } elseif (interface_exists($name)) {
                $this->definitions[$name] = new InterfaceDefinition($name, $value);
            } else {
                throw new ProgrammerException('Invalid container definition for name ' . $name);
            }
        }
    }

    /**
     * You must use name and args. Optionally you can provide object
     *
     * @param array $definition
     */
    public function add(array $definition): void
    {
        $newDefinition = new ClassDefinition($definition['name'], $definition['args'] ?? []);
        $newDefinition->object = $definition['object'] ?? null;
        $this->definitions[$definition['name']] = $newDefinition;
    }

    /**
     * @param array<string,Definition> $definitions
     * @return void
     */
    public function addDefinitions(array $definitions): void
    {
        foreach ($definitions as $id => $definition) {
            $this->definitions[$id] = $definition;
        }
    }

    /**
     * @param string[] $ids
     * @return Definition[]
     * @throws \pjpawel\LightApi\Container\ContainerNotFoundException
     */
    public function getDefinitions(array $ids): array
    {
        /** @var Definition[] $definitions */
        $definitions = [];
        foreach ($ids as $id) {
            if (!isset($this->definitions[$id])) {
                throw new ContainerNotFoundException();
            }
            $definitions[] = $this->definitions[$id];
        }
        return $definitions;
    }

    /**
     * @param array<string,string> $services
     * @return void
     */
    public function prepareContainerLocator(array $services): void
    {
        foreach ($services as $internalId => $serviceId) {
            $this->definitions[$internalId] = new AliasDefinition($internalId, $serviceId);
        }
    }
}
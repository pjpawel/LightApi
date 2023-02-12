<?php

namespace pjpawel\LightApi\Container;

use pjpawel\LightApi\Kernel\ProgrammerException;
use Psr\Container\ContainerInterface;

class ContainerLoader implements ContainerInterface
{

    use ContainerTrait;

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
                throw new ProgrammerException('Invalid container definition for name ' . $name);
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
        $newDefinition = new ClassDefinition($definition['name'], $definition['args'] ?? []);
        $newDefinition->object = $definition['object'] ?? null;
        $this->definitions[$definition['name']] = $newDefinition;
    }

    /**
     * @param Definition[] $definitions
     * @return void
     */
    public function addDefinitions(array $definitions): void
    {
        foreach ($definitions as $definition) {
            $this->definitions[] = $definition;
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




}
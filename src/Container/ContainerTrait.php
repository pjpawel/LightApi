<?php

namespace pjpawel\LightApi\Container;

use ReflectionClass;

trait ContainerTrait
{
    /**
     * @var Definition[]
     */
    public array $definition;

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
        } elseif ($definition instanceof InDirectDefinition) {
            $this->definitions[$id]->object = $this->get($definition->className);
        } else {
            /** @var DefinedDefinition $definition */
            $this->definitions[$id]->object = $definition->load();
        }
    }

}
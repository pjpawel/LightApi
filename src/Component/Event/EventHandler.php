<?php

namespace pjpawel\LightApi\Component\Event;

use pjpawel\LightApi\Exception\ProgrammerException;

class EventHandler
{

    public const KERNEL_AFTER_BOOT = 'KernelAfterBoot';
    public const KERNEL_BEFORE_REQUEST = 'KernelBeforeRequest';
    public const KERNEL_AFTER_REQUEST = 'KernelAfterRequest';
    public const KERNEL_BEFORE_COMMAND = 'KernelBeforeCommand';
    public const KERNEL_AFTER_COMMAND = 'KernelAfterCommand';
    public const KERNEL_ON_DESTRUCT = 'KernelOnDestruct';

    /**
     * @var array<string,EventInterface>
     */
    private array $events = [];

    public function registerEvent(string $id, ?callable $callable, null|array|string $data): void
    {
        if (isset($callable)) {
            $this->events[$id] = new CallbackEvent($callable, $data ?? []);
        } elseif (is_array($data)) {
            $dataLen = count($data);
            if ($dataLen < 2) {
                throw new ProgrammerException('Cannot pass less than 2 arguments to ' . $id);
            } elseif ($dataLen == 2) {
                $this->events[$id] = new DefinedFunctionEvent($data[0], $data[1]);
            } else {
                $this->events[$id] = new MethodEvent($data[0], $data[1], array_slice($data, 2));
            }
        } elseif (is_string($data)){
            $this->events[$id] = new DefinedFunctionEvent($data, []);
        } else {
            throw new ProgrammerException('Cannot register event ' . $id . ' without callable or data');
        }
    }

    public function has(string $eventId): bool
    {
        return isset($this->events[$eventId]);
    }

    /**
     * @param string $eventId
     * @return mixed
     * @throws ProgrammerException|\Exception
     */
    public function trigger(string $eventId): mixed
    {
        if (!isset($this->events[$eventId])) {
            throw new ProgrammerException(sprintf('Event %s was called, but it seems that there is no such event', $eventId));
        }
        return $this->events[$eventId]->run();
    }

    /**
     * This method does not throw exception if Event is not registered
     *
     * @param string $eventId
     * @return mixed
     */
    public function tryTriggering(string $eventId): mixed
    {
        if (!isset($this->events[$eventId])) {
            return null;
        }
        return $this->events[$eventId]->run();
    }
}
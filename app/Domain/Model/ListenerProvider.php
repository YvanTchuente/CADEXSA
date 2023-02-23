<?php

namespace Cadexsa\Domain\Model;

use Psr\EventDispatcher\ListenerProviderInterface;

class ListenerProvider implements ListenerProviderInterface
{
    /**
     * @var \Closure[][]
     */
    private array $listeners = [];

    public function addListener(callable $listener)
    {
        $listener = \Closure::fromCallable($listener);
        $eventType = (new \ReflectionFunction($listener))->getParameters()[0]->getType()->getName();
        $this->listeners[$eventType][] = $listener;
    }

    public function getListenersForEvent(object $event): iterable
    {
        $eventType = get_class($event);
        $listeners = $this->listeners[$eventType];

        return $listeners;
    }
}

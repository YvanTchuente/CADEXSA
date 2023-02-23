<?php

namespace Cadexsa\Domain\Model;

use Psr\EventDispatcher\StoppableEventInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

class EventDispatcher implements EventDispatcherInterface
{
    private ListenerProviderInterface $provider;

    private static ?self $instance = null;

    public static function begin(ListenerProviderInterface $provider)
    {
        self::$instance = new EventDispatcher($provider);
    }

    public static function getInstance()
    {
        return self::$instance;
    }

    public static function end()
    {
        self::$instance = null;
    }

    public function __construct(ListenerProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    public function dispatch(object $event)
    {
        $listeners = $this->provider->getListenersForEvent($event);

        foreach ($listeners as $listener) {
            if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
                break;
            }
            $listener($event);
        }

        return $event;
    }
}

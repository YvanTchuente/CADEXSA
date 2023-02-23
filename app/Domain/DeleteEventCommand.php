<?php

namespace Cadexsa\Domain;

use Cadexsa\Domain\Model\Persistence;
use Cadexsa\Domain\Model\Event\Event;

class DeleteEventCommand implements Command, Originator
{
    private Event $event;

    public function __construct(int $eventId = null)
    {
        if ($eventId) {
            $this->event = Persistence::eventRepository()->findById($eventId);
        }
    }

    public function saveToMemento(): Memento
    {
        $memento = new DeleteEventState($this, $this->event);
        return $memento;
    }

    public function restore(Memento $memento)
    {
        $event = $memento->getState();
        $this->event = $event;
    }

    public function execute()
    {
        return Persistence::eventRepository()->remove($this->event);
    }

    public function undo()
    {
        Persistence::eventRepository()->add($this->event);
    }
}

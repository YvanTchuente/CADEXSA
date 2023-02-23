<?php

namespace Cadexsa\Domain;

use Cadexsa\Domain\Model\Event\Event;

class DeleteEventState implements Memento
{
    private Event $event;

    private string $date;

    private string $originator;

    public function __construct(DeleteEventCommand $originator, Event $event)
    {
        $this->event = $event;
        $this->date = date("Y-m-d H:i:s");
        $this->originator = get_class($originator);
    }

    public function originator()
    {
        return $this->originator;
    }

    public function getName()
    {
        return $this->event->getName();
    }

    public function getDate()
    {
        return $this->date;
    }

    public function getState()
    {
        return $this->event;
    }
}

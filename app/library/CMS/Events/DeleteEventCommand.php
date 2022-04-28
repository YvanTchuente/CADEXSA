<?php

namespace Application\CMS\Events;

use Application\Generic\Command;
use Application\Generic\Memento;
use Application\Generic\Originator;
use Application\MiddleWare\Request;
use Application\MiddleWare\TextStream;

class DeleteEventCommand implements Command, Originator
{
    /**
     * @var EventManager
     */
    protected $EventManager;

    /**
     * @var int 
     */
    protected $ID;

    /**
     * @var EventInterface
     */
    protected $item;

    public function __construct(EventManager $EventManager)
    {
        $this->EventManager = $EventManager;
    }

    public function setID(int $ID)
    {
        $this->ID = $ID;
        $this->initialize();
    }

    public function saveToMemento(): memento
    {
        $state = array('ID' => $this->ID, 'item' => $this->item);
        $originator = clone $this;
        $originator->clear(); // Clears all data
        $memento = new DeleteEventState($originator, $state);
        return $memento;
    }

    private function clear()
    {
        unset($this->ID);
        unset($this->item);
    }

    public function restore(Memento $m)
    {
        $state = $m->getState();
        $this->ID  = $state['ID'];
        $this->item = $state['item'];
    }

    protected function initialize()
    {
        $this->item = $this->EventManager->get($this->ID);
    }

    public function execute()
    {
        if (empty($this->ID)) {
            throw new \RuntimeException("Error executing command: Event's ID is not set");
        }
        return $this->EventManager->delete($this->ID);
    }

    public function undo()
    {
        if (!isset($this->item)) {
            throw new \RuntimeException("Error undoing last command: Event object not given");
        }
        $deadline = explode(" ", $this->item->getDeadlineDate());
        $content = array(
            'title' => $this->item->getTitle(),
            'venue' => $this->item->getVenue(),
            'body' => $this->item->getBody(),
            'thumbnail' => $this->item->getThumbnail(),
            'publication_date' => $this->item->getPublicationDate(),
            'deadline' => $deadline[0],
            'deadline_time' => substr($deadline[1], 0, -3)
        );
        $body = new TextStream(json_encode($content));
        $request = (new Request())->withBody($body);
        return (bool) $this->EventManager->save($request);
    }
}

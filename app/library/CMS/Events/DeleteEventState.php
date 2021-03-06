<?php

namespace Application\CMS\Events;

use Application\Generic\Memento;
use Application\CMS\EventInterface;
use Application\Generic\Originator;

class DeleteEventState implements Memento
{
    /**
     * @var int 
     */
    private $ID;

    /**
     * @var EventInterface
     */
    private $item;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $date;

    /**
     * @var Originator
     */
    private $originator;

    public function __construct(Originator $DeleteEventCommand, array $state)
    {
        $this->ID = $state['ID'];
        $this->item = $state['item'];
        $this->date = date("Y-m-d H:i:s");
        $this->name = $this->item->getTitle();
        $this->originator = $DeleteEventCommand;
    }

    public function getOriginator()
    {
        return $this->originator;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getState()
    {
        $state = array('ID' => $this->ID, 'item' => $this->item);
        return $state;
    }
}

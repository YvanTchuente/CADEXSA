<?php

namespace Application\CMS\Events;

use Application\Generic\Memento;
use Application\CMS\EventInterface;

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

    public function getState(): array
    {
        $state = array('ID' => $this->ID, 'item' => $this->item);
        return $state;
    }

    public function setState(array $state)
    {
        $this->ID = $state['ID'];
        $this->item = $state['item'];
    }
}

<?php

namespace Application\CMS\News;

use Application\Generic\Memento;
use Application\CMS\NewsInterface;

class DeleteNewsState implements Memento
{
    /**
     * @var int 
     */
    private $ID;

    /**
     * @var NewsInterface
     */
    private $item;
    
    /**
     * @var string
     */
    private $tag;

    public function getState(): array
    {
        $state = array('ID' => $this->ID, 'item' => $this->item, 'tag' => $this->tag);
        return $state;
    }

    public function setState(array $state)
    {
        $this->ID = $state['ID'];
        $this->item = $state['item'];
        $this->tag = $state['tag'];
    }
}

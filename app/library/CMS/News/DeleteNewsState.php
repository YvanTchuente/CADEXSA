<?php

namespace Application\CMS\News;

use Application\Generic\Memento;
use Application\CMS\NewsInterface;
use Application\Generic\Originator;

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

    public function __construct(Originator $DeleteNewsCommand, array $state)
    {
        $this->ID = $state['ID'];
        $this->item = $state['item'];
        $this->tag = $state['tag'];
        $this->date = date("Y-m-d H:i:s");
        $this->name = $this->item->getTitle();
        $this->originator = $DeleteNewsCommand;
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
        $state = array('ID' => $this->ID, 'item' => $this->item, 'tag' => $this->tag);
        return $state;
    }
}

<?php

namespace Application\CMS\Gallery;

use Application\Generic\Memento;
use Application\Generic\Originator;
use Application\CMS\PictureInterface;

class DeletePictureState implements Memento
{
    /**
     * @var int
     */
    private $ID;

    /**
     * @var PictureInterface
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

    public function __construct(Originator $DeletePictureCommand, array $state)
    {
        $this->ID = $state['ID'];
        $this->item = $state['item'];
        $this->date = date("Y-m-d H:i:s");
        $this->name = $this->item->getName();
        $this->originator = $DeletePictureCommand;
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

<?php

namespace Application\CMS\News;

use Application\CMS\Gallery\{
    PictureManager,
    PictureInterface,
    DeletePictureState
};
use Application\Generic\Command;
use Application\Generic\Memento;
use Application\Generic\Originator;

class DeletePictureCommand implements Command, Originator
{
    /**
     * @var PictureManager
     */
    protected $PictureManager;

    /**
     * @var int
     */
    protected $ID;

    /**
     * @var PictureInterface
     */
    protected $item;

    public function __construct(PictureManager $PictureManager)
    {
        $this->PictureManager = $PictureManager;
    }

    public function setID(int $ID)
    {
        $this->ID = $ID;
        $this->initialize();
    }

    public function saveToMemento(): Memento
    {
        $state = array('ID' => $this->ID, 'item' => $this->item);
        $originator = clone $this;
        $originator->clear(); // Clears all data
        $memento = new DeletePictureState($originator, $state);
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
        $this->item = $this->PictureManager->get($this->ID);
    }

    public function execute()
    {
        if (!empty($this->ID)) {
            return $this->PictureManager->delete($this->ID);
        } else {
            throw new \RuntimeException("Picture's ID is not set");
        }
    }

    /**
     * @todo Implement PictureManager's 'save' method to implement this method
     */
    public function undo()
    {
        return true;
    }
}

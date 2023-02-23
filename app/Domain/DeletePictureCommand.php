<?php

namespace Cadexsa\Domain;

use Cadexsa\Domain\Model\Persistence;
use Cadexsa\Domain\Model\Picture\Picture;

class DeletePictureCommand implements Command, Originator
{
    private Picture $picture;

    public function __construct(int $pictureId = null)
    {
        if ($pictureId) {
            $this->picture = Persistence::pictureRepository()->findById($pictureId);
        }
    }

    public function saveToMemento(): Memento
    {
        $memento = new DeletePictureState($this, $this->picture);
        return $memento;
    }

    public function restore(Memento $memento)
    {
        $state = $memento->getState();
        $this->id  = $state['id'];
        $this->item = $state['item'];
    }

    public function execute()
    {
        Persistence::pictureRepository()->remove($this->picture);
    }

    /**
     * @todo Implement PictureManager's 'save' method to implement this method
     */
    public function undo()
    {
        return true;
    }
}

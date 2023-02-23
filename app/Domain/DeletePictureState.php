<?php

namespace Cadexsa\Domain;

use Cadexsa\Domain\Model\Picture\Picture;

class DeletePictureState implements Memento
{
    private Picture $picture;

    private string $date;

    private string $originator;

    public function __construct(DeletePictureCommand $originator, Picture $picture)
    {
        $this->picture = $picture;;
        $this->date = date("Y-m-d H:i:s");
        $this->originator = get_class($originator);
    }

    public function originator()
    {
        return $this->originator;
    }

    public function getName()
    {
        return basename($this->picture->getLocation());
    }

    public function getDate()
    {
        return $this->date;
    }

    public function getState()
    {
        return $this->picture;
    }
}

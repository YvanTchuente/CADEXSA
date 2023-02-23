<?php

declare(strict_types=1);

namespace Cadexsa\Domain\Model\Picture;

use Cadexsa\Domain\Model\INull;
use Cadexsa\Domain\Model\NullContentTrait;

class MissingPicture extends Picture implements INull
{
    use NullContentTrait;

    public function getLocation()
    {
        return "";
    }

    public function getDescription()
    {
        return "";
    }

    public function shotOn()
    {
        return "";
    }

    public function setName(string $name)
    {
        return $this;
    }

    public function setLocation(string $location)
    {
        return $this;
    }

    public function setDescription(string $description)
    {
        return $this;
    }

    public function setShotOn(string $shotOn)
    {
        return $this;
    }
}

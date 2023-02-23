<?php

declare(strict_types=1);

namespace Cadexsa\Domain\Model\Event;

use Cadexsa\Domain\Model\INull;
use Cadexsa\Domain\Model\NullContentTrait;

class MissingEvent extends Event implements INull
{
    use NullContentTrait;

    public function getName()
    {
        return "Missing event";
    }

    public function getDescription()
    {
        return "";
    }

    public function getImage()
    {
        return "";
    }

    public function getVenue()
    {
        return "";
    }

    public function getOccurrenceDate()
    {
        return new \DateTime;
    }

    public function getStatus()
    {
        return Status::UPCOMING;
    }

    public function occurred()
    {
        return false;
    }

    public function setName(string $name)
    {
        return $this;
    }

    public function setDescription(string $description)
    {
        return $this;
    }

    public function setImage(string $image)
    {
        return $this;
    }

    public function setVenue(string $venue)
    {
        return $this;
    }

    public function setOccurrenceDate(string $timestamp)
    {
        return $this;
    }

    public function setStatus(Status $status)
    {
        return $this;
    }
}

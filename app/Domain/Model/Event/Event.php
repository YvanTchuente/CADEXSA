<?php

declare(strict_types=1);

namespace Cadexsa\Domain\Model\Event;

use Cadexsa\Domain\Model\Content;

/**
 * Represents an event.
 */
class Event extends Content
{
    /**
     * The event's name.
     */
    private string $name;

    /**
     * The event's venue.
     */
    private string $venue;

    /**
     * The date and time of the event's occurrence.
     */
    private string $occursOn;

    /**
     * The event's description.
     */
    private string $description;

    /**
     * The event's status
     */
    private Status $status;

    /**
     * The URI of the representative image.
     */
    private string $image;

    public function __construct(int $id, string $name, string $description, string $occurrenceDate, string $venue, string $image, string $publicationDate = null)
    {
        parent::__construct($id, $publicationDate);
        $this->setName($name);
        $this->setVenue($venue);
        $this->setOccurrenceDate($occurrenceDate);
        $this->setDescription($description);
        $this->setStatus(Status::UPCOMING);
        $this->setImage($image);
    }

    /**
     * Retrieves the event's name.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Retrieves the event's description.
     */
    public function getDescription(bool $summmarize = false)
    {
        if ($summmarize) {
            return strip_tags(substr($this->description, 0, 250));
        } else {
            return $this->description;
        }
    }

    /**
     * Retrieves the event's representative image.
     * 
     * @return string The URI of the image.
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Retrieves the event's venue.
     */
    public function getVenue()
    {
        return $this->venue;
    }

    /**
     * Retrieves the date and time at which the event occurs.
     */
    public function getOccurrenceDate()
    {
        return new \DateTime($this->occursOn);
    }

    /**
     * Retrieves the event's status.
     *
     * @return Status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Determines whether or not the event has occurred.
     *
     * @return bool True if the event occurred or false otherwise.
     */
    public function occurred()
    {
        return $this->status == Status::OCCURRED;
    }

    /**
     * Sets the event's name.
     *
     * @param string $name A case-sentitive name.
     */
    public function setName(string $name)
    {
        if (!$name) {
            throw new \LengthException("The name is required.");
        }
        $this->name = $name;

        return $this;
    }

    /**
     * Sets the event's description.
     */
    public function setDescription(string $description)
    {
        if (!$description) {
            throw new \LengthException("The description is required.");
        }
        $this->description = $description;

        return $this;
    }

    /**
     * Sets the event's representative image.
     *
     * @param string $uri The URI of the image.
     */
    public function setImage(string $uri)
    {
        if (!filter_var($uri, FILTER_VALIDATE_URL)) {
            throw new \DomainException("$uri is not a valid URI.");
        }
        $this->image = $uri;

        return $this;
    }

    /**
     * Sets the event's venue.
     */
    public function setVenue(string $venue)
    {
        if (!$venue) {
            throw new \LengthException("The venue is required.");
        }
        $this->venue = $venue;

        return $this;
    }

    /**
     * Sets the timestamp at which the event occurs.
     */
    public function setOccurrenceDate(string $timestamp)
    {
        $this->occursOn = $this->validateTimestamp($timestamp);

        return $this;
    }

    /**
     * Sets the event's status.
     */
    public function setStatus(Status $status)
    {
        $this->status = $status;

        return $this;
    }
}

<?php

declare(strict_types=1);

namespace Cadexsa\Domain\Model\Picture;

use Cadexsa\Domain\Model\Content;

/**
 * Represents a gallery picture.
 */
class Picture extends Content
{
    /**
     * The URI of the picture.
     */
    private string $location;

    /**
     * The picture's description.
     */
    private string $description;

    /**
     * The date and time at which the picture was shot.
     */
    private string $shotOn;

    public function __construct(int $id, string $url, string $description, string $shotOn, string $publicationDate = null)
    {
        parent::__construct($id, $publicationDate);
        $this->setLocation($url);
        $this->setDescription($description);
        $this->setShotOn($shotOn);
    }

    /**
     * Retrieves the picture's location.
     * 
     * @return string The URI of the picture.
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Retrieves the picture's description.
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Retrieves a date and time at which the picture was shot.
     */
    public function shotOn()
    {
        return new \DateTime($this->shotOn);
    }

    /**
     * Sets the location of the picture.
     */
    public function setLocation(string $uri)
    {
        if (!$uri) {
            throw new \DomainException("The location is required.");
        }
        $this->location = "/images/gallery/" . $uri;
    }

    /**
     * Sets the picture's description.
     */
    public function setDescription(string $description)
    {
        if (!$description) {
            throw new \DomainException("The description is required.");
        }
        $this->description = $description;
    }

    /**
     * Sets the date and time at which the picture was shot.
     */
    public function setShotOn(string $timestamp)
    {
        $this->shotOn = $this->validateTimestamp($timestamp);
    }
}

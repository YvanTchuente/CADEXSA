<?php

declare(strict_types=1);

namespace Application\CMS\Gallery;

use Application\CMS\Item;
use Application\CMS\PictureInterface;

class Picture extends Item implements PictureInterface
{
    /**
     * Path to directory where the gallery pictures are stored
     */
    protected const REPOSITORY = '/static/images/gallery/';

    /**
     * @var string|null
     */
    protected $name;

    /**
     * Location on the server
     * 
     * @var string|null
     */
    protected $location;

    /**
     * @var string|null
     */
    protected $description;

    /**
     * Date of snapshot
     * 
     * @var string|null
     */
    protected $snapshotDate;

    public function __construct(
        int $ID,
        string $name = null,
        string $description = null,
        string $snapshotDate = null,
        string $publicationDate = null
    ) {
        $this->ID = $ID;
        $this->name = $name;
        $this->location = self::REPOSITORY . $this->name;
        $this->description = $description;
        $this->snapshotDate = $snapshotDate;
        $this->publicationDate = $publicationDate;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getLocation()
    {
        return $this->location;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getSnapshotDate()
    {
        return $this->date;
    }

    public function setName(string $name)
    {
        if (!$name) {
            throw new \InvalidArgumentException("Invalid argument");
        }
        $this->name = $name;
        if ($this->location === self::REPOSITORY) {
            $this->location = self::REPOSITORY . $this->name;
        }
        return $this;
    }

    public function setLocation(string $location)
    {
        if (!$location) {
            throw new \InvalidArgumentException("Invalid argument");
        }
        $this->location = $location;
        return $this;
    }

    public function setDescription(string $description)
    {
        if (!$description) {
            throw new \InvalidArgumentException("Invalid argument");
        }
        $this->description = $description;
        return $this;
    }

    public function setSnapshotDate(string $date)
    {
        if (!$date || !strtotime($date)) {
            throw new \InvalidArgumentException("Invalid date");
        }
        $this->date = $date;
        return $this;
    }
}

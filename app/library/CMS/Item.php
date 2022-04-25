<?php

declare(strict_types=1);

namespace Application\CMS;

class Item implements ItemInterface
{
    /**
     * @var int|null
     */
    protected $ID;

    /**
     * @var string|null
     */
    protected $publicationDate;

    public function getID()
    {
        return $this->ID;
    }

    public function getPublicationDate()
    {
        return $this->publicationDate;
    }

    public function setPublicationDate(string $publicationDate)
    {
        if (!$publicationDate || !strtotime($publicationDate)) {
            throw new \InvalidArgumentException("Invalid date");
        }
        $this->publicationDate = $publicationDate;
        return $this;
    }

    protected function setID(int $ID)
    {
        if (!$ID) {
            throw new \InvalidArgumentException("Invalid ID");
        }
        $this->ID = $ID;
    }
}

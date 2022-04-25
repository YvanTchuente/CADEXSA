<?php

declare(strict_types=1);

namespace Application\CMS\News;

use Application\CMS\Article;
use Application\CMS\NewsInterface;

class News extends Article implements NewsInterface
{
    /**
     * @var int|null
     */
    protected $authorID;

    /**
     * @var NewsStatus
     */
    protected $status;

    /**
     * @var string|null
     */
    protected $creationDate;

    public function __construct(
        int $ID = null,
        int $authorID = null,
        string $title = null,
        string $body = null,
        string $thumbnail = null,
        string $publicationDate = null,
        string $creationDate = null,
        NewsStatus $status = NewsStatus::NOT_PUBLISHED
    ) {
        $this->ID = $ID;
        $this->status = $status;
        $this->body = $body;
        $this->title = $title;
        $this->authorID = $authorID;
        $this->thumbnail = $thumbnail;
        $this->publicationDate = $publicationDate;
        $this->creationDate = $creationDate;
    }

    public function getCreationDate()
    {
        return $this->creationDate;
    }

    public function getAuthorID()
    {
        return $this->authorID;
    }

    public function wasPublished()
    {
        $status = $this->status->value;
        $wasPublished = (bool) $status;
        return $wasPublished;
    }

    public function setCreationDate(string $creationDate)
    {
        if (!$creationDate || !strtotime($creationDate)) {
            throw new \InvalidArgumentException("Invalid date");
        }
        $this->creationDate = $creationDate;
        return $this;
    }

    public function setAuthorID(int $authorID)
    {
        if (!$authorID) {
            throw new \InvalidArgumentException("Invalid ID");
        }
        $this->authorID = $authorID;
        return $this;
    }

    public function setStatus(NewsStatus $status)
    {
        $this->status = $status;
        return $this;
    }
}

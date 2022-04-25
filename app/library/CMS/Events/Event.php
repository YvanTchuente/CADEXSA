<?php

declare(strict_types=1);

namespace Application\CMS\Events;

use Application\CMS\Article;
use Application\CMS\EventInterface;

class Event extends Article implements EventInterface
{
    /**
     * @var string|null
     */
    protected $venue;

    /**
     * @var string|null
     */
    protected $deadlineDate;

    /**
     * @var EventStatus
     */
    protected $status;

    public function __construct(
        int $ID = null,
        string $title = null,
        string $body = null,
        string $venue = null,
        string $thumbnail = null,
        string $publicationDate = null,
        string $deadlineDate = null,
        EventStatus $status = EventStatus::NOT_HAPPENED
    ) {
        $this->ID = $ID;
        $this->title = $title;
        $this->body = $body;
        $this->venue = $venue;
        $this->status = $status;
        $this->thumbnail = $thumbnail;
        $this->publicationDate = $publicationDate;
        $this->deadlineDate = $deadlineDate;
    }

    public function getVenue()
    {
        return $this->venue;
    }

    public function getDeadlineDate()
    {
        return $this->deadlineDate;
    }

    public function hasHappened()
    {
        $status = $this->status->value;
        $hasHappened = false;
        if ($status === 1) {
            $hasHappened = true;
        }
        return $hasHappened;
    }

    public function setVenue(string $venue)
    {
        if (empty($venue)) {
            throw new \InvalidArgumentException("Invalid venue");
        }
        $this->venue = $venue;
        return $this;
    }

    public function setDeadlineDate(string $deadlineDate)
    {
        if (empty($deadlineDate) || !strtotime($deadlineDate)) {
            throw new \InvalidArgumentException("Invalid date");
        }
        $this->deadlineDate = $deadlineDate;
        return $this;
    }

    public function setStatus(EventStatus $status)
    {
        $this->status = $status;
        return $this;
    }
}

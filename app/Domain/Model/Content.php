<?php

declare(strict_types=1);

namespace Cadexsa\Domain\Model;

use Cadexsa\Domain\Model\Entity;

/**
 * CMS content supertype
 */
abstract class Content extends Entity
{
    public function __construct(int $id, string $publicationDate = null)
    {
        parent::__construct($id);
        $this->setPublicationDate($publicationDate ?? date("Y-m-d H:i:s"));
    }

    /**
     * The publication timestamp.
     */
    protected string $publishedOn;

    /**
     * Retrieves the date and time of publication.
     */
    public function getPublicationDate()
    {
        return new \DateTime($this->publishedOn);
    }

    /**
     * Sets the date and time of publication.
     * 
     * @throws \DomainException if timestamp is invalid.
     */
    public function setPublicationDate(string $timestamp)
    {
        $this->publishedOn = $this->validateTimestamp($timestamp);
    }
}

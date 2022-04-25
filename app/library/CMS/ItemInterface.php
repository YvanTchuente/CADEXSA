<?php

namespace Application\CMS;

/**
 * Interface common to items managed by a CMS
 */
interface ItemInterface
{
    /**
     * Returns the ID of the item
     *
     * @return int
     */
    public function getID();

    /**
     * Returns the date and time of when the item was published
     *
     * @return string
     */
    public function getPublicationDate();

    /**
     * Sets the publication date and time of when the item was published
     * 
     * @param string $publicationDate The date and time of when this item was published
     * 
     * @return static
     * 
     * @throws \InvalidArgumentException For invalid dates
     */
    public function setPublicationDate(string $publicationDate);
}

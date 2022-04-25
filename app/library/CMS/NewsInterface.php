<?php

namespace Application\CMS;

use Application\CMS\News\NewsStatus;
use Application\CMS\ArticleInterface;

/**
 * Describes a news article
 */
interface NewsInterface extends ArticleInterface
{
    /**
     * Returns the date and time when the news article was created
     *
     * @return string
     */
    public function getCreationDate();

    /**
     * Returns the ID of the author of the news article
     *
     * @return int
     */
    public function getAuthorID();

    /**
     * Determines whether a news article was published
     *
     * @return bool true if the article was published and false otherwise
     */
    public function wasPublished();

    /**
     * Sets the date and time of when the article was created
     *
     * @param string $creationDate The date and time of when the article was created
     * 
     * @return static
     * 
     * @throws \InvalidArgumentException For invalid dates
     */
    public function setCreationDate(string $creationDate);

    /**
     * Sets the ID of the author of the news article
     *
     * @param string $authorID ID of the author
     * 
     * @return static
     */
    public function setAuthorID(int $authorID);

    /**
     * Sets the status of the news article
     *
     * @param NewsStatus $status
     * 
     * @return static
     */
    public function setStatus(NewsStatus $status);
}

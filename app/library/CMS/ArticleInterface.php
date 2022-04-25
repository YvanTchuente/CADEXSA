<?php

declare(strict_types=1);

namespace Application\CMS;

use Application\CMS\ItemInterface;

/**
 * Interface common to articles
 */
interface ArticleInterface extends ItemInterface
{
    /**
     * Returns the title of the article
     *
     * @return string
     */
    public function getTitle();

    /**
     * Returns the body of the article
     *
     * @return string
     */
    public function getBody();

    /**
     * Returns the URL of picture representing
     * the article
     *
     * @return string
     */
    public function getThumbnail();

    /**
     * Sets the title of the article 
     *
     * @param string $title Title of the article
     * 
     * @return static
     * 
     * @throws \InvalidArgumentException For invalid 
     */
    public function setTitle(string $title);

    /**
     * Sets the body of the article
     *
     * @param string $body Body of the article
     * 
     * @return static
     * 
     * @throws \InvalidArgumentException For invalid values
     */
    public function setBody(string $body);

    /**
     * Sets the representative picture of the article
     *
     * @param string $thumbnail URI/filename of the picture
     * 
     * @return static
     * 
     * @throws \InvalidArgumentException For invalid filename/URIs
     */
    public function setThumbnail(string $thumbnail);
}

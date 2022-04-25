<?php

declare(strict_types=1);

namespace Application\CMS;

use Application\CMS\ItemInterface;

/**
 * Describes a gallery picture
 */
interface PictureInterface extends ItemInterface
{
    /**
     * Returns the filename of the picture
     *
     * @return string
     */
    public function getName();

    /**
     * Returns the location of the picture on the server
     *
     * @return string
     */
    public function getLocation();

    /**
     * Returns the description of the picture
     *
     * @return string
     */
    public function getDescription();

    /**
     * Returns the date and time when the picture was made
     *
     * @return string
     */
    public function getSnapshotDate();

    /**
     * Sets the location on the server of the picture
     *
     * @param string $location The location of the picture on the server
     * 
     * @return static
     */
    public function setLocation(string $location);

    /**
     * Sets the description of the picture
     *
     * @param string $description A description of the picture
     * 
     * @return static
     */
    public function setDescription(string $description);

    /**
     * Sets the date and time of when the picture was shot
     *
     * @param string $snapshotDate The date and time of picture's snapshot
     * 
     * @return static
     */
    public function setSnapshotDate(string $snapshotDate);
}

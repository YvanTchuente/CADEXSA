<?php

namespace Application\CMS;

use Application\CMS\ArticleInterface;

/**
 * Describes an event article
 */
interface EventInterface extends ArticleInterface
{
    /**
     * Retrieves the venue of the event
     *
     * @return string
     */
    public function getVenue();

    /**
     * Retrieves the deadline's date and time of the event
     *
     * @return string
     */
    public function getDeadlineDate();

    /**
     * Determine whether the event has happened 
     *
     * @return bool
     */
    public function hasHappened();

    /**
     * Sets the venue of the event
     *
     * @param string $venue The location of the venue of the event
     * 
     * @return static
     * 
     * @throws \InvalidArgumentException If venue argument is empty
     */
    public function setVenue(string $venue);

    /**
     * Sets the deadline's date and time of the event
     *
     * @param string $deadline The date and time of the deadline
     * 
     * @return static
     * 
     * @throws \InvalidArgumentException If an invalid date and time is given
     */
    public function setDeadlineDate(string $deadlineDate);

    /**
     * Sets the status of an event
     *
     * @param \Application\CMS\Events\EventStatus $status Whether the event happened or not
     * 
     * @return static
     */
    public function setStatus(\Application\CMS\Events\EventStatus $status);
}

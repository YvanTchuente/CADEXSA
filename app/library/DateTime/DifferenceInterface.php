<?php

declare(strict_types=1);

namespace Application\DateTime;

/**
 * Describes an instance that computes date and time differences
 */
interface DifferenceInterface
{
    /**
     * Sets the reference time
     *
     * @return static
     */
    public function setReferenceTime(\DateTimeInterface $time);

    /**
     * Sets the target time
     *
     * @return static
     */
    public function setTargetTime(\DateTimeInterface $time);

    /**
     * Returns the difference between the configured reference time and target time
     * in a human-readable format
     * 
     * The difference might be in the short form e.g '2 mins ago', '3 days left'
     * or the full form e.g '2 years 3 months 2 weeks 3 days 5 hours 3mins 5s ago' 
     * 
     * @param string $form Either 'short' or 'full'
     * 
     * @return string
     */
    public function diff(string $form = 'short');
}

<?php

declare(strict_types=1);

namespace Application\DateTime;

interface TimeDurationInterface
{
    public function setReferenceTime(\DateTime $time);

    public function setTargetTime(\DateTime $time);

    /**
     * Compute the longest time elapsed since Reference time to Target time
     * 
     * @return string
     * 
     * @throws \RuntimeException
     **/
    public function getLongestDuration();

    /**
     * Compute the time duration between Reference time and Target time
     * 
     * Compute the time elapsed since Reference time to Target time in the
     * case Reference time is behind Target time or Time remaining otherwise.
     * If it returns an array, the array contains an element keyed 'status'
     * referring to whether Reference time is behind (past) or ahead (future)
     * of Target time
     * 
     * @param string $representation Form in which the time duration should be rendered,
     *                               Possible values are 'string' and 'array'
     * 
     * @return string|array
     */
    public function getDuration(string $representation = "string");
}

<?php

declare(strict_types=1);

namespace Cadexsa\Domain\Model;

interface IntervalFormatter
{
    /**
     * Formats a time interval.
     */
    public function format(\DateInterval $interval): string;
}

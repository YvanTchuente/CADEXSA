<?php

declare(strict_types=1);

namespace Cadexsa\Domain\Model\ExStudent;

/**
 * Represents ex-student account status.
 */
enum Status: int
{
    case SUSPENDED = 0;
    case UNACTIVATED = 1;
    case ACTIVE = 2;

    public function label()
    {
        return ucfirst(strtolower($this->name));
    }

    public function isSuperiorTo(Status $status)
    {
        return ($this->value < $status->value);
    }
}

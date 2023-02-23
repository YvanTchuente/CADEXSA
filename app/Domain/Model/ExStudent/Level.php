<?php

declare(strict_types=1);

namespace Cadexsa\Domain\Model\ExStudent;

/**
 * Ex-student priviledge levels
 */
enum Level: int
{
    case VISITOR = 0;
    case REGULAR = 1;
    case EDITOR = 2;
    case ADMINISTRATOR = 3;

    public function label()
    {
        return ucfirst(strtolower($this->name));
    }

    public function isSuperiorTo(Level $status)
    {
        return ($this->value > $status->value);
    }
}

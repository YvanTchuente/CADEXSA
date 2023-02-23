<?php

declare(strict_types=1);

namespace Cadexsa\Domain\Model\ExStudent;

/**
 * Representation of a non-authenticated existing ex-student
 */
class UnknownExStudent extends NullExStudent
{
    public function getUsername()
    {
        return "Unknown ex-student";
    }
}

<?php

declare(strict_types=1);

namespace Cadexsa\Domain\Model\ExStudent;

/**
 * Representation of a non-existent ex-student
 */
class MissingExStudent extends NullExStudent
{
    public function getUsername()
    {
        return "Missing ex-student";
    }
}

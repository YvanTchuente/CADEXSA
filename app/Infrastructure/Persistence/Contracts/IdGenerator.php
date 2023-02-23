<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure\Persistence\Contracts;

interface IdGenerator
{
    /**
     * Generates a unique identifier.

     * @return int The identifier.
     */
    public function generateId();
}

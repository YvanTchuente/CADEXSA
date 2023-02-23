<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure\Persistence\Contracts;

use Cadexsa\Domain\Model\Entity;

/**
 * Defines a specification.
 */
interface Specification
{
    /**
     * Determines if the specification is satisfied by a given domain entity.
     * 
     * @param Entity $entity A domain entity object.
     *
     * @return bool
     */
    public function isSatisfiedBy(Entity $entity);
}

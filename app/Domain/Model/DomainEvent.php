<?php

namespace Cadexsa\Domain\Model;

/**
 * Defines a domain event.
 */
interface DomainEvent
{
    public function occurredOn(): \DateTimeImmutable;
}

<?php

declare(strict_types=1);

namespace Cadexsa\Domain\Model\ExStudent;

use Cadexsa\Domain\Model\DomainEvent;

class ExStudentLoggedIn implements DomainEvent
{
    private \DateTimeImmutable $occurredOn;

    public function __construct(public readonly string $username)
    {
        $this->occurredOn = new \DateTimeImmutable;
    }

    public function occurredOn(): \DateTimeImmutable
    {
        return $this->occurredOn;
    }
}

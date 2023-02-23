<?php

declare(strict_types=1);

namespace Cadexsa\Domain\Model\ExStudent;

use Cadexsa\Domain\Model\DomainEvent;

class ExstudentRegistered implements DomainEvent
{
    public readonly ExStudent $exstudent;
    
    private \DateTimeImmutable $occurredOn;

    public function __construct(ExStudent $exstudent)
    {
        $this->exstudent = $exstudent;
        $this->occurredOn = new \DateTimeImmutable;
    }

    public function occurredOn(): \DateTimeImmutable
    {
        return $this->occurredOn;
    }
}

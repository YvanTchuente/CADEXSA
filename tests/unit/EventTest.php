<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Application\CMS\Events\Event;
use Application\CMS\Events\EventStatus;

class EventTest extends TestCase
{
    /** @var Event */
    private $event;

    public function setUp(): void
    {
        $this->event = (new Event())
            ->setVenue("PK 21, Douala")
            ->setDeadlineDate("2022-05-31 13:30:00")
            ->setStatus(EventStatus::from(1));
    }

    public function testGetVenue()
    {
        $this->assertSame("PK 21, Douala", $this->event->getVenue());
    }

    public function testGetDeadLineDate()
    {
        $this->assertSame("2022-05-31 13:30:00", $this->event->getDeadlineDate());
    }

    public function testHasHappened()
    {
        $this->assertTrue($this->event->hasHappened());
    }

    public function testSetVenue()
    {
        $this->event->setVenue("PK 22, Douala");
        $this->assertSame("PK 22, Douala", $this->event->getVenue());

        $this->expectException('InvalidArgumentException');
        $this->event->setVenue("");
    }

    public function testDeadlineDate()
    {
        $this->event->setDeadlineDate("2022-05-31 14:26:30");
        $this->assertSame("2022-05-31 14:26:30", $this->event->getDeadlineDate());

        $this->expectException('InvalidArgumentException');
        $this->event->setDeadlineDate("");
    }
}

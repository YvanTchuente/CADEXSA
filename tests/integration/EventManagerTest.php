<?php

declare(strict_types=1);

namespace Tests\Integration;

use Tests\Mocks\Connection;
use PHPUnit\Framework\TestCase;
use Application\DateTime\TimeDuration;
use Application\CMS\Events\EventManager;

class EventManagerTest extends TestCase
{
    /** @var EventManager */
    private $EventManager;

    public function setUp(): void
    {
        $connector = new Connection();
        $this->EventManager = new EventManager($connector);
    }

    public function testGet()
    {
        $event = $this->EventManager->get(1);
        $this->assertSame(1, $event->getID());

        $this->expectException('Exception');
        $event = $this->EventManager->get(2);
    }

    public function testList()
    {
        $list = $this->EventManager->list(1);
        $this->assertContainsOnly('\Application\CMS\EventInterface', $list);
    }

    public function testPreview()
    {
        $timeDuration = $this->createStub(TimeDuration::class);
        $timeDuration->method('getLongestDuration')->willReturn('1 days ago');
        $preview = $this->EventManager->preview(1, $timeDuration);

        $this->assertArrayHasKey('id', $preview);
        $this->assertArrayHasKey('thumbnail', $preview);
        $this->assertArrayHasKey('title', $preview);
        $this->assertArrayHasKey('body', $preview);
        $this->assertArrayHasKey('deadline', $preview);
    }

    public function testItemExist()
    {
        $this->assertTrue($this->EventManager->exists(1));
        $this->assertFalse($this->EventManager->exists(2));
    }
}

<?php

declare(strict_types=1);

namespace Tests\Integration;

use Tests\Mocks\Connection;
use PHPUnit\Framework\TestCase;
use Application\CMS\News\TagManager;
use Application\CMS\News\NewsManager;
use Application\DateTime\TimeDuration;

class NewsManagerTest extends TestCase
{
    /** @var NewsManager */
    private $NewsManager;

    public function setUp(): void
    {
        $connector = new Connection();
        $TagManager = $this->createStub(TagManager::class);
        $this->NewsManager = new NewsManager($connector, $TagManager);
    }

    public function testGet()
    {
        $news = $this->NewsManager->get(1);
        $this->assertSame(1, $news->getID());

        $this->expectException('Exception');
        $news = $this->NewsManager->get(2);
    }

    public function testList()
    {
        $list = $this->NewsManager->list(1);
        $this->assertContainsOnly('\Application\CMS\NewsInterface', $list);
    }

    public function testPreview()
    {
        $timeDuration = $this->createStub(TimeDuration::class);
        $timeDuration->method('getLongestDuration')->willReturn('2 days ago');
        $preview = $this->NewsManager->preview(1, $timeDuration);

        $this->assertArrayHasKey('id', $preview);
        $this->assertArrayHasKey('thumbnail', $preview);
        $this->assertArrayHasKey('title', $preview);
        $this->assertArrayHasKey('body', $preview);
        $this->assertArrayHasKey('timeDiff', $preview);
    }

    public function testItemExist()
    {
        $this->assertTrue($this->NewsManager->exists(1));
        $this->assertFalse($this->NewsManager->exists(2));
    }

    public function testPublish()
    {
        $this->assertTrue($this->NewsManager->publish(1));
    }
}

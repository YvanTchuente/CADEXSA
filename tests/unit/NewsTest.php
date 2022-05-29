<?php

declare(strict_types=1);

namespace Tests\Unit;

use Application\CMS\News\News;
use PHPUnit\Framework\TestCase;
use Application\CMS\News\NewsStatus;

class NewsTest extends TestCase
{
    /** @var News */
    private $news;

    public function setUp(): void
    {
        $this->news = (new News())
            ->setAuthorID(1)
            ->setCreationDate("2022-05-05 02:27:50")
            ->setStatus(NewsStatus::from(1));
    }

    public function testGetAuthorID()
    {
        $this->assertSame(1, $this->news->getAuthorID());
    }

    public function testGetCreationDate()
    {
        $this->assertSame("2022-05-05 02:27:50", $this->news->getCreationDate());
    }

    public function testWasPublished()
    {
        $this->assertTrue($this->news->wasPublished());
    }

    public function testSetAuthorID()
    {
        $this->news->setAuthorID(2);
        $this->assertSame(2, $this->news->getAuthorID());

        $this->expectException('InvalidArgumentException');
        $this->news->setAuthorID(0);
    }

    public function testSetCreationDate()
    {
        $this->news->setCreationDate("2022-05-30 13:21:30");
        $this->assertSame("2022-05-30 13:21:30", $this->news->getCreationDate());

        $this->expectException('InvalidArgumentException');
        $this->news->setCreationDate("");
    }
}

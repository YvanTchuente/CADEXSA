<?php

declare(strict_types=1);

namespace Tests\Integration;

use Tests\Mocks\Connection;
use Application\CMS\News\Tag;
use Application\CMS\News\News;
use PHPUnit\Framework\TestCase;
use Application\CMS\News\TagManager;
use Application\CMS\News\NewsManager;

class TagManagerTest extends TestCase
{
    /** @var TagManager */
    private $TagManager;

    public function setUp(): void
    {
        $connector = new Connection();
        $this->TagManager = new TagManager($connector);
    }

    public function testGet()
    {
        $tag = $this->TagManager->get(1);
        $this->assertSame(1, $tag->getID());

        $this->expectException('Exception');
        $tag = $this->TagManager->get(10);
    }

    public function testGetTag()
    {
        $news = (new NewsManager(new Connection))->get(1);
        $tag = $this->TagManager->getTag($news);
        $this->assertSame("School", $tag->getName());
    }

    public function testValidate()
    {
        $this->assertSame(4, $this->TagManager->validate("School"));
        $this->assertFalse($this->TagManager->validate("Breaking Bad"));
    }

    public function testGetArticles()
    {
        $news = new News();
        $tag = $this->createStub(Tag::class);
        $tag->method('getID')->willReturn(1);

        $NewsManager = $this->createStub(NewsManager::class);
        $NewsManager->method('get')->willReturn($news);

        $articles = $this->TagManager->getArticles($tag, $NewsManager);
        $this->assertContainsOnly("\Application\CMS\NewsInterface", $articles);
    }

    public function testList()
    {
        $list = $this->TagManager->list(5);
        $this->assertContainsOnly(Tag::class, $list);
    }

    public function testItemExist()
    {
        $this->assertTrue($this->TagManager->exists(1));
        $this->assertFalse($this->TagManager->exists(10));
    }
}

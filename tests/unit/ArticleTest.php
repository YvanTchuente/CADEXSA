<?php

declare(strict_types=1);

namespace Tests\Unit;

use Application\CMS\Article;
use PHPUnit\Framework\TestCase;

class ArticleTest extends TestCase
{
    /** @var Article */
    private $article;

    public function setUp(): void
    {
        $this->article = new Article;
        $this->article->setTitle("Test Title 1");
        $this->article->setBody("Test Body 1");
        $this->article->setThumbnail("img5.jpg");
    }

    public function testGetTitle()
    {
        $this->assertSame("Test Title 1", $this->article->getTitle());
    }

    public function testGetBody()
    {
        $this->assertSame("Test Body 1", $this->article->getBody());
    }

    public function testGetThumbnail()
    {
        $this->assertSame("img5.jpg", $this->article->getThumbnail());
    }

    public function testSetTitle()
    {
        $this->article->setTitle("Test Title 2");
        $this->assertSame("Test Title 2", $this->article->getTitle());

        $this->expectException('InvalidArgumentException');
        $this->article->setTitle("");
    }

    public function testSetBody()
    {
        $this->article->setBody("Test Body 2");
        $this->assertSame("Test Body 2", $this->article->getBody());

        $this->expectException('InvalidArgumentException');
        $this->article->setBody("");
    }

    public function testSetThumbnail()
    {
        $this->article->setThumbnail("img6.jpg");
        $this->assertSame("img6.jpg", $this->article->getThumbnail());

        $this->expectException('InvalidArgumentException');
        $this->article->setThumbnail("");
    }
}

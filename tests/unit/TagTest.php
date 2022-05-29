<?php

declare(strict_types=1);

namespace Tests\Unit;

use Application\CMS\News\Tag;
use PHPUnit\Framework\TestCase;

class TagTest extends TestCase
{
    private $tag;

    public function setUp(): void
    {
        $this->tag = new Tag(1, 'Events');
    }

    public function testGetID()
    {
        $this->assertSame(1, $this->tag->getID());
    }

    public function testGetName()
    {
        $this->assertSame('Events', $this->tag->getName());
    }
}

<?php

declare(strict_types=1);

namespace Tests\Integration;

use Tests\Mocks\Connection;
use PHPUnit\Framework\TestCase;
use Application\CMS\Gallery\PictureManager;

class PictureManagerTest extends TestCase
{
    /** @var PictureManager */
    private $PictureManager;

    public function setUp(): void
    {
        $connector = new Connection();
        $this->PictureManager = new PictureManager($connector);
    }

    public function testGet()
    {
        $event = $this->PictureManager->get(1);
        $this->assertSame(1, $event->getID());

        $this->expectException('Exception');
        $event = $this->PictureManager->get(20);
    }

    public function testList()
    {
        $list = $this->PictureManager->list(10);
        $this->assertContainsOnly('\Application\CMS\PictureInterface', $list);
    }

    public function testItemExist()
    {
        $this->assertTrue($this->PictureManager->exists(1));
        $this->assertFalse($this->PictureManager->exists(20));
    }
}

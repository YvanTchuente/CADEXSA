<?php

declare(strict_types=1);

namespace Tests\Unit;

use Application\CMS\Item;
use PHPUnit\Framework\TestCase;

class ItemTest extends TestCase
{
    /** @var Item */
    private $item;

    public function setUp(): void
    {
        $this->item = new Item;
        $this->item->setID(2);
        $this->item->setPublicationDate("2022-05-31 05:20:30");
    }

    public function testGetID()
    {
        $this->assertSame(2, $this->item->getID());
    }

    public function testGetPublicationDate()
    {
        $this->assertSame("2022-05-31 05:20:30", $this->item->getPublicationDate());
    }

    public function testSetID()
    {
        $this->item->setID(1);
        $this->assertSame(1, $this->item->getID());

        $this->expectException('InvalidArgumentException');
        $this->item->setID(0);
    }

    public function testSetPublicationDate()
    {
        $this->item->setPublicationDate("2022-05-05 03:39:59");
        $this->assertSame("2022-05-05 03:39:59", $this->item->getPublicationDate());
    }

    /** @test */
    public function it_throws_exception_date_is_empty()
    {
        $this->expectException('InvalidArgumentException');
        $this->item->setPublicationDate("");
    }

    /** @test */
    public function it_throws_exception_date_is_invalid()
    {
        $this->expectException('InvalidArgumentException');
        $this->item->setPublicationDate("sdfdfgsgsgdf");
    }
}

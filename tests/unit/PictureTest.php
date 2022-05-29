<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Application\CMS\Gallery\Picture;

class PictureTest extends TestCase
{
    /** @var Picture */
    private $picture;

    public function setUp(): void
    {
        $this->picture = (new Picture())
            ->setName('img2.jpg')
            ->setDescription('De create building thinking about your requirment')
            ->setSnapshotDate('2021-12-11 00:24:55');
    }

    public function testGetName()
    {
        $this->assertSame('img2.jpg', $this->picture->getName());
    }

    public function testGetLocation()
    {
        $location = "/static/images/gallery/img2.jpg";
        $this->assertSame($location, $this->picture->getLocation());
    }

    public function testGetDescription()
    {
        $this->assertSame('De create building thinking about your requirment', $this->picture->getDescription());
    }

    public function testGetSnapshotDate()
    {
        $this->assertSame('2021-12-11 00:24:55', $this->picture->getSnapshotDate());
    }

    public function testSetName()
    {
        $this->picture->setName("img6.jpg");
        $this->assertSame("img6.jpg", $this->picture->getName());

        $this->expectException('InvalidArgumentException');
        $this->picture->setName("");
    }

    public function testSetLocation()
    {
        $this->picture->setLocation("img6.jpg");
        $this->assertSame("img6.jpg", $this->picture->getLocation());

        $this->expectException('InvalidArgumentException');
        $this->picture->setLocation("");
    }

    public function testSetDescription()
    {
        $this->picture->setDescription("New description");
        $this->assertSame("New description", $this->picture->getDescription());

        $this->expectException('InvalidArgumentException');
        $this->picture->setDescription("");
    }

    public function testSetSnapshotDate()
    {
        $this->picture->setSnapshotDate("2022-05-05 03:39:59");
        $this->assertSame("2022-05-05 03:39:59", $this->picture->getSnapshotDate());
    }

    /** @test */
    public function it_throws_exception_date_is_empty()
    {
        $this->expectException('InvalidArgumentException');
        $this->picture->setSnapshotDate("");
    }

    /** @test */
    public function it_throws_exception_date_is_invalid()
    {
        $this->expectException('InvalidArgumentException');
        $this->picture->setSnapshotDate("sdfdfgsgsgdf");
    }
}

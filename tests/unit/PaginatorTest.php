<?php

declare(strict_types=1);

namespace Tests\Unit;

use Application\CMS\News\News;
use Application\CMS\Paginator;
use PHPUnit\Framework\TestCase;
use Application\CMS\News\NewsStatus;
use Application\CMS\News\NewsManager;

class PaginatorTest extends TestCase
{
    private $list;
    private $itemsPerPage;

    public function setUp(): void
    {
        $news1 = new News();
        $news1->setID(1)
            ->setAuthorID(1)
            ->setTitle("Little updates to CMS mechanism")
            ->setBody("Body of the article")
            ->setThumbnail("/static/iames/gallery/img1.jpg")
            ->setPublicationDate("2022-05-27 16:12:30")
            ->setCreationDate("2020-05-20 04:30:45")
            ->setStatus(NewsStatus::from(1));

        $news2 = new News();
        $news2->setID(2)
            ->setAuthorID(1)
            ->setTitle("Chat messaging has exploded this last decade")
            ->setBody("Body of the article")
            ->setThumbnail("/static/iames/gallery/img1.jpg")
            ->setPublicationDate("2022-05-27 16:12:30")
            ->setCreationDate("2020-05-20 04:30:45")
            ->setStatus(NewsStatus::from(1));

        $this->list = [$news1, $news2];
        $this->itemsPerPage = 1;
    }

    public function testGetTotalNumberOfPages()
    {
        $manager = $this->createStub(NewsManager::class);
        $manager->method('list')->will($this->returnValue($this->list));
        $paginator = new Paginator($manager, $this->itemsPerPage);
        $total_number_of_pages = 2;
        $this->assertSame($total_number_of_pages, $paginator->getTotalNumberOfPages());
    }

    public function testPaginate()
    {
        $manager = $this->createStub(NewsManager::class);
        $manager->method('list')->will($this->returnValue([$this->list[0]]));
        $paginator = new Paginator($manager, $this->itemsPerPage);

        $page = 1;
        $expected_batch = [$this->list[0]];
        $actual_batch = $paginator->paginate($page);
        $this->assertEquals($expected_batch, $actual_batch);

        $manager = $this->createStub(NewsManager::class);
        $manager->method('list')->will($this->onConsecutiveCalls($this->list, [$this->list[1]]));
        $paginator = new Paginator($manager, $this->itemsPerPage);

        $page = 2;
        $expected_batch = [$this->list[1]];
        $actual_batch = $paginator->paginate($page);
        $this->assertEquals($expected_batch, $actual_batch);

        $manager = $this->createStub(NewsManager::class);
        $manager->method('list')->will($this->returnValue([]));
        $paginator = new Paginator($manager, $this->itemsPerPage);

        $page = 3;
        $actual_batch = $paginator->paginate($page);
        $this->assertEquals([], $actual_batch);
    }

    /** @test */
    public function it_throws_exception_on_pageNumber_greater_max_pageNumber()
    {
        $manager = $this->createStub(NewsManager::class);
        $manager->method('list')->will($this->returnValue($this->list));
        $paginator = new Paginator($manager, $this->itemsPerPage);
        $page = 3;
        $this->expectException('InvalidArgumentException');
        $paginator->paginate($page);
    }
}

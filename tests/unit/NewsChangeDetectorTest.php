<?php

declare(strict_types=1);

namespace Tests\Unit;

use Application\CMS\News\Tag;
use Application\CMS\News\News;
use PHPUnit\Framework\TestCase;
use Application\MiddleWare\Stream;
use Application\MiddleWare\Request;
use Application\CMS\News\TagManager;
use Application\CMS\News\NewsManager;
use Application\CMS\NewsChangeDetector;

class NewsChangeDetectorTest extends TestCase
{
    /** @test */
    public function test_no_changes_were_made()
    {
        $params = [
            'title' => 'Title',
            'tag' => 'students',
            'body' => 'The body of the article',
        ];
        $content = json_encode($params);

        $stream = $this->createStub(Stream::class);
        $stream->method('getContents')->willReturn($content);

        $request = $this->createStub(Request::class);
        $request->method('getBody')->willReturn($stream);

        $news = $this->getMockBuilder(News::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getTitle', 'getBody'])
            ->getMock();
        $news->method('getTitle')->willReturn($params['title']);
        $news->method('getBody')->willReturn($params["body"]);

        $manager = $this->createStub(NewsManager::class);
        $manager->method('get')->willReturn($news);

        $tag = $this->createStub(Tag::class);
        $tag->method('getName')->willReturn('students');

        $tagManager = $this->createStub(TagManager::class);
        $tagManager->method('getTag')->willReturn($tag);

        $detector = new NewsChangeDetector($request, 1, $manager, $tagManager);
        $this->assertFalse($detector->detect());
    }

    /** 
     * @test
     * @dataProvider provider
     */
    public function test_changes_were_made(array $dataset)
    {
        $content = json_encode($dataset['params']);

        $stream = $this->createStub(Stream::class);
        $stream->method('getContents')->willReturn($content);

        $request = $this->createStub(Request::class);
        $request->method('getBody')->willReturn($stream);

        $news = $this->getMockBuilder(News::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getTitle', 'getBody'])
            ->getMock();
        $news->method('getTitle')->willReturn($dataset['dbParams']['title']);
        $news->method('getBody')->willReturn("Hey folks, this is a news article about cadexsa");

        $manager = $this->createStub(NewsManager::class);
        $manager->method('get')->willReturn($news);

        $tag = $this->createStub(Tag::class);
        $tag->method('getName')->willReturn('students');

        $tagManager = $this->createStub(TagManager::class);
        $tagManager->method('getTag')->willReturn($tag);

        $detector = new NewsChangeDetector($request, 1, $manager, $tagManager);
        $changes = $detector->detect();
        $expected_changes = ['body' => 'Its body'];
        $this->assertNotEquals($expected_changes, $changes);
    }

    public function provider()
    {
        $dbParams = [
            'title' => 'Title',
            'tag' => 'students',
            'body' => 'The body of the article',
        ];
        $dataset1 = [
            'dbParams' => $dbParams,
            'params' => [
                'title' => 'Title',
                'tag' => 'Its venu',
                'body' => 'Hey folks, this is a news article about cadexsa',
            ]
        ];
        $dataset2 = [
            'dbParams' => $dbParams,
            'params' => [
                'title' => 'Title',
                'tag' => 'School',
                'body' => 'The body of the article',
            ]
        ];
        $dataset3 = [
            'dbParams' => $dbParams,
            'params' => [
                'title' => 'New Title',
                'tag' => 'students',
                'body' => 'The body of the article',
            ]
        ];

        $datasets = [
            ['dataset' => $dataset1],
            ['dataset' => $dataset2],
            ['dataset' => $dataset3]
        ];
        return $datasets;
    }
}

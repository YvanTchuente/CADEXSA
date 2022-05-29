<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Application\CMS\Events\Event;
use Application\MiddleWare\Stream;
use Application\MiddleWare\Request;
use Application\CMS\EventChangeDetector;
use Application\CMS\Events\EventManager;

class EventChangeDetectorTest extends TestCase
{
    /** @test */
    public function test_no_changes_were_made()
    {
        $params = [
            'title' => 'Title',
            'venue' => 'Its venu',
            'body' => 'The body of the article',
            'deadline' => '2022-05-28',
            'deadline_time' => '05:36',
        ];
        $content = json_encode($params);

        $stream = $this->createStub(Stream::class);
        $stream->method('getContents')->willReturn($content);

        $request = $this->createStub(Request::class);
        $request->method('getBody')->willReturn($stream);

        $event = $this->getMockBuilder(Event::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getTitle', 'getVenue', 'getBody', 'getDeadlineDate'])
            ->getMock();
        $event->method('getTitle')->willReturn($params['title']);
        $event->method('getVenue')->willReturn($params['venue']);
        $timestamp = implode(" ", [$params['deadline'], $params['deadline_time']]);
        $event->method('getDeadlineDate')->willReturn($timestamp);
        $event->method('getBody')->willReturn($params["body"]);

        $manager = $this->createStub(EventManager::class);
        $manager->method('get')->willReturn($event);

        $detector = new EventChangeDetector($request, 1, $manager);
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

        $event = $this->getMockBuilder(Event::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getTitle', 'getVenue', 'getBody', 'getDeadlineDate'])
            ->getMock();
        $event->method('getTitle')->willReturn($dataset['dbParams']['title']);
        $event->method('getVenue')->willReturn($dataset['dbParams']['venue']);
        $timestamp = implode(" ", [$dataset['dbParams']['deadline'], $dataset['dbParams']['deadline_time']]);
        $event->method('getDeadlineDate')->willReturn($timestamp);
        $event->method('getBody')->willReturn("Hey folks, this is a news article about cadexsa");

        $manager = $this->createStub(EventManager::class);
        $manager->method('get')->willReturn($event);

        $detector = new EventChangeDetector($request, 1, $manager);
        $changes = $detector->detect();
        $expected_changes = ['description' => 'Its body'];
        $this->assertNotEquals($expected_changes, $changes);
    }

    public function provider()
    {
        $dbParams = [
            'title' => 'Title',
            'venue' => 'Its venu',
            'body' => 'The body of the article',
            'deadline' => '2022-05-28',
            'deadline_time' => '05:36'
        ];
        $dataset1 = [
            'dbParams' => $dbParams,
            'params' => [
                'title' => 'Title',
                'venue' => 'Its venu',
                'body' => 'Hey folks, this is a news article about cadexsa',
                'deadline' => '2022-05-28',
                'deadline_time' => '05:36'
            ]
        ];
        $dataset2 = [
            'dbParams' => $dbParams,
            'params' => [
                'title' => 'Title',
                'venue' => 'Douala, pk21',
                'body' => 'The body of the article',
                'deadline' => '2022-05-28',
                'deadline_time' => '05:36'
            ]
        ];
        $dataset3 = [
            'dbParams' => $dbParams,
            'params' => [
                'title' => 'News about our beloved high school',
                'venue' => 'Its venu',
                'body' => 'The body of the article',
                'deadline' => '2022-05-28',
                'deadline_time' => '05:36'
            ]
        ];
        $dataset4 = [
            'dbParams' => $dbParams,
            'params' => [
                'title' => 'Title',
                'venue' => 'Its venu',
                'body' => 'The body of the article',
                'deadline' => '2022-05-25',
                'deadline_time' => '05:36'
            ]
        ];
        $dataset5 = [
            'dbParams' => $dbParams,
            'params' => [
                'title' => 'Title',
                'venue' => 'Its venu',
                'body' => 'The body of the article',
                'deadline' => '2022-05-25',
                'deadline_time' => '05:20'
            ]
        ];

        $datasets = [
            ['dataset' => $dataset1],
            ['dataset' => $dataset2],
            ['dataset' => $dataset3],
            ['dataset' => $dataset4],
            ['dataset' => $dataset5]
        ];
        return $datasets;
    }
}

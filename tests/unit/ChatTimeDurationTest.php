<?php

declare(strict_types=1);

namespace Tests\Unit;

use DateTime;
use PHPUnit\Framework\TestCase;
use Application\Membership\MemberStatus;
use Application\DateTime\ChatTimeDuration;

class ChatTimeDurationTest extends TestCase
{
    private $timeDuration;

    public function setUp(): void
    {
        $this->timeDuration = new ChatTimeDuration();
        $this->timeDuration->setReferenceTime(new DateTime('2020-11-13 09:15:13'));
    }

    /** @test */
    public function it_throws_exception_when_targetTime_is_behind_referenceTime()
    {
        $this->timeDuration->setTargetTime(new DateTime('2019-12-09 09:15:13'));
        $this->expectExceptionMessage("Reference time is ahead of Target time");
        $this->timeDuration->getLongestDuration();
    }

    /** @test */
    public function it_throws_exception_when_referenceTime_not_given()
    {
        $timeDuration = new ChatTimeDuration();
        $this->expectExceptionMessage("Reference time was not given");
        $timeDuration->getLongestDuration();
    }

    /** @test */
    public function it_throws_execption_when_targetTime_not_given()
    {
        $timeDuration = new ChatTimeDuration(new DateTime());
        $this->expectExceptionMessage("Target time was not given");
        $timeDuration->getLongestDuration();
    }

    /**
     * @dataProvider provider
     */
    public function testGetLongestDuration(array $datetime)
    {
        // Given a target time
        $targetTime = new DateTime($datetime['targetTime']);
        if (isset($datetime['status'])) $this->timeDuration->set_status($datetime['status']);
        $this->timeDuration->setTargetTime($targetTime);

        // Assert that the expected largest time duration is returned by the method
        $this->assertSame($datetime['expected_duration'], $this->timeDuration->getLongestDuration());
    }

    public function provider(): array
    {
        // Reference time is : 2020-11-13 09:15:13
        $datetime1 = "2020-12-13 09:15:13";
        $datetime2 = "2021-01-13 09:15:13";
        $datetime3 = "2020-11-14 09:15:13";
        $datetime4 = "2020-11-15 09:15:13";
        $datetime5 = "2020-11-13 10:15:13";
        $datetime6 = "2020-11-13 11:15:13";
        $datetime7 = "2020-11-13 09:16:13";
        $datetime8 = "2020-11-13 09:17:13";
        $datetime9 = "2020-11-13 09:15:14";
        $datetime10 = "2020-11-13 09:15:43";
        $datetime11 = "2020-11-13 09:15:13.215";
        $datetime12 = "2020-11-13 09:15:18";
        return array(
            array('datetime' => [
                'targetTime' => $datetime1,
                'expected_duration' => '1 month ago'
            ]),
            array('datetime' => [
                'targetTime' => $datetime2,
                'expected_duration' => '2 months ago'
            ]),
            array('datetime' => [
                'targetTime' => $datetime3,
                'expected_duration' => '1 day ago'
            ]),
            array('datetime' => [
                'targetTime' => $datetime4,
                'expected_duration' => '2 days ago'
            ]),
            array('datetime' => [
                'targetTime' => $datetime5,
                'expected_duration' => '1 hour ago'
            ]),
            array('datetime' => [
                'targetTime' => $datetime6,
                'expected_duration' => '2 hours ago'
            ]),
            array('datetime' => [
                'targetTime' => $datetime7,
                'expected_duration' => '1 min ago'
            ]),
            array('datetime' => [
                'targetTime' => $datetime8,
                'expected_duration' => '2 mins ago'
            ]),
            array('datetime' => [
                'targetTime' => $datetime9,
                'expected_duration' => '1 sec ago'
            ]),
            array('datetime' => [
                'targetTime' => $datetime10,
                'expected_duration' => '30 secs ago'
            ]),
            array('datetime' => [
                'targetTime' => $datetime11,
                'expected_duration' => 'Offline'
            ]),
            array('datetime' => [
                'targetTime' => $datetime11,
                'expected_duration' => 'Active',
                'status' => MemberStatus::ONLINE
            ]),
            array('datetime' => [
                'targetTime' => $datetime12,
                'expected_duration' => 'Active',
                'status' => MemberStatus::ONLINE
            ])
        );
    }
}

<?php

declare(strict_types=1);

namespace Tests\Unit;

use DateTime;
use PHPUnit\Framework\TestCase;
use Application\DateTime\TimeDuration;

class TimeDurationTest extends TestCase
{
    private $timeDuration;

    public function setUp(): void
    {
        $this->timeDuration = new TimeDuration();
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
    public function it_throws_exception_when_referenceTime_not_given_1()
    {
        $timeDuration = new TimeDuration();
        $this->expectExceptionMessage("Reference time was not given");
        $timeDuration->getLongestDuration();
    }

    /** @test */
    public function it_throws_exception_when_referenceTime_not_given_2()
    {
        $timeDuration = new TimeDuration();
        $this->expectExceptionMessage("Reference time was not given");
        $timeDuration->getDuration();
    }

    /** @test */
    public function it_throws_execption_when_targetTime_not_given_1()
    {
        $timeDuration = new TimeDuration(new DateTime());
        $this->expectExceptionMessage("Target time was not given");
        $timeDuration->getLongestDuration();
    }

    /** @test */
    public function it_throws_execption_when_targetTime_not_given_2()
    {
        $timeDuration = new TimeDuration(new DateTime());
        $this->expectExceptionMessage("Target time was not given");
        $timeDuration->getDuration();
    }

    /** @test */
    public function it_throws_exception_after_invalid_parameter_passed()
    {
        $timeDuration = new TimeDuration(new DateTime('2020-11-13 10:20:25'), new DateTime());
        $this->expectExceptionMessage("Invalid representation parameter given");
        $timeDuration->getDuration("row");
    }

    public function testGetDuration()
    {
        // Given a target time ahead of reference time
        $this->timeDuration->setTargetTime(new DateTime('2022-05-10 07:53:26'));

        $expected_duration_as_string = "1 year 5 months 26 days 22 hours 38 mins 13 secs ago";
        $expected_duration_as_array = ['y' => 1, 'm' => 5, 'd' => 26, 'h' => 22, 'i' => 38, 's' => 13, 'status' => 'past'];

        $duration = $this->timeDuration->getDuration();
        // Assert that the string representation of the duration is what is expected
        $this->assertSame($expected_duration_as_string, $duration);
        // Assert that the array representation of the duration is what is expected
        $duration = $this->timeDuration->getDuration('array');
        $this->assertSame($expected_duration_as_array, $duration);

        // Given a target time behind reference time
        $this->timeDuration->setTargetTime(new DateTime('2019-12-09 09:15:13'));

        $expected_duration_as_string = "11 months 4 days left";
        $expected_duration_as_array = ['m' => 11, 'd' => 4, 'status' => 'future'];

        // Assert that the string representation of the duration is what is expected
        $duration = $this->timeDuration->getDuration();
        $this->assertSame($expected_duration_as_string, $duration);
        // Assert that the array representation of the duration is what is expected
        $duration = $this->timeDuration->getDuration('array');
        $this->assertSame($expected_duration_as_array, $duration);
    }

    /**
     * @dataProvider provider_for_getLongestDuration
     */
    public function testGetLongestDuration(array $datetime)
    {
        // Given a target time
        $targetTime = new DateTime($datetime['targetTime']);
        $this->timeDuration->setTargetTime($targetTime);

        // Assert that the expected largest time duration is returned by the method
        $this->assertSame($datetime['expected_duration'], $this->timeDuration->getLongestDuration());
    }

    public function provider_for_getLongestDuration(): array
    {
        // Reference time is : 2020-11-13 09:15:13
        $datetime1 = "2021-12-13 09:15:13";
        $datetime2 = "2022-12-13 09:15:13";
        $datetime3 = "2020-12-13 09:15:13";
        $datetime4 = "2021-01-13 09:15:13";
        $datetime5 = "2020-11-20 09:15:13";
        $datetime6 = "2020-11-27 09:15:13";
        $datetime7 = "2020-11-14 09:15:13";
        $datetime8 = "2020-11-15 09:15:13";
        $datetime9 = "2020-11-13 10:15:13";
        $datetime10 = "2020-11-13 11:15:13";
        $datetime11 = "2020-11-13 09:16:13";
        $datetime12 = "2020-11-13 09:17:13";
        $datetime13 = "2020-11-13 09:15:14";
        $datetime14 = "2020-11-13 09:15:15";
        $datetime15 = "2020-11-13 09:15:13.215";
        return array(
            array('datetime' => [
                'targetTime' => $datetime1,
                'expected_duration' => '1 year ago'
            ]),
            array('datetime' => [
                'targetTime' => $datetime2,
                'expected_duration' => '2 years ago'
            ]),
            array('datetime' => [
                'targetTime' => $datetime3,
                'expected_duration' => '1 month ago'
            ]),
            array('datetime' => [
                'targetTime' => $datetime4,
                'expected_duration' => '2 months ago'
            ]),
            array('datetime' => [
                'targetTime' => $datetime5,
                'expected_duration' => '1 week ago'
            ]),
            array('datetime' => [
                'targetTime' => $datetime6,
                'expected_duration' => '2 weeks ago'
            ]),
            array('datetime' => [
                'targetTime' => $datetime7,
                'expected_duration' => '1 day ago'
            ]),
            array('datetime' => [
                'targetTime' => $datetime8,
                'expected_duration' => '2 days ago'
            ]),
            array('datetime' => [
                'targetTime' => $datetime9,
                'expected_duration' => '1 hour ago'
            ]),
            array('datetime' => [
                'targetTime' => $datetime10,
                'expected_duration' => '2 hours ago'
            ]),
            array('datetime' => [
                'targetTime' => $datetime11,
                'expected_duration' => '1 min ago'
            ]),
            array('datetime' => [
                'targetTime' => $datetime12,
                'expected_duration' => '2 mins ago'
            ]),
            array('datetime' => [
                'targetTime' => $datetime13,
                'expected_duration' => '1 sec ago'
            ]),
            array('datetime' => [
                'targetTime' => $datetime14,
                'expected_duration' => '2 secs ago'
            ]),
            array('datetime' => [
                'targetTime' => $datetime15,
                'expected_duration' => '0 sec ago'
            ])
        );
    }
}

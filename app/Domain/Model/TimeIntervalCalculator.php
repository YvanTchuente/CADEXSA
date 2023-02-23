<?php

declare(strict_types=1);

namespace Cadexsa\Domain\Model;

use Cadexsa\Domain\ServiceRegistry;
use Cadexsa\Domain\Model\ExStudent\State;

class TimeIntervalCalculator
{
    /**
     * Calculates the time interval between any two given timestamps.
     * 
     * @param \DateTime|string $start The start timestamp.
     * @param \DateTime|string $end The end timestamp.
     * @param IntervalFormatter $formatter A formatter to format the interval.
     * 
     * @return string The formatted time interval.
     */
    public function interval(\DateTime|string $start, \DateTime|string $end, IntervalFormatter $formatter = new ShortFormFormatter)
    {
        if (is_string($start)) {
            $start = new \DateTime($start);
        }
        if (is_string($end)) {
            $end = new \DateTime($end);
        }
        $interval = $end->diff($start);
        $interval = $formatter->format($interval);
        return $interval;
    }

    /**
     * Determines the time elapsed since the ex-student's recorded last activity.
     */
    public function elapsedTimeSinceLastActivity(int $exStudentId)
    {
        $exstudent = Persistence::exStudentRepository()->findById($exStudentId);
        $state = $exstudent->state();
        switch ($state) {
            case State::ONLINE:
                $stmt = app()->database->getConnection()->pdo->prepare("SELECT last_activity FROM online_members WHERE exStudentId = ?");
                $stmt->execute([$exstudent->getId()]);
                $lastActivity = $stmt->fetch(\PDO::FETCH_ASSOC)['last_activity'];
                $timeElapsed = ServiceRegistry::timeIntervalCalculator()->interval($lastActivity, new \DateTime);
                break;
            case State::OFFLINE:
                $lastSessionOn = $exstudent->getLastSessionDate() ?? new \DateTime;
                $timeElapsed = ServiceRegistry::timeIntervalCalculator()->interval($lastSessionOn, new \DateTime);
                break;
        }
        return $timeElapsed;
    }
}

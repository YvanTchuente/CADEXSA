<?php

declare(strict_types=1);

namespace Application\DateTime;

class TimeDuration implements TimeDurationInterface
{
    protected ?\DateTime $referenceTime;
    protected ?\DateTime $targetTime;
    protected ?\DateInterval $interval;

    public function __construct(?\DateTime $referenceTime = null, ?\DateTime $targetTime = null)
    {
        $this->referenceTime = $referenceTime;
        $this->targetTime = $targetTime;
    }

    public function setReferenceTime(\DateTime $time)
    {
        $this->referenceTime = $time;
    }

    public function setTargetTime(\DateTime $time)
    {
        $this->targetTime = $time;
    }

    protected function getInterval()
    {
        $this->interval = $this->targetTime->diff($this->referenceTime, true);
    }

    public function getLongestDuration()
    {
        if (empty($this->referenceTime)) {
            throw new \RuntimeException("Reference time was not given");
        }
        if (empty($this->targetTime)) {
            throw new \RuntimeException("Target time was not given");
        }
        if ($this->referenceTime > $this->targetTime) {
            throw new \RuntimeException("Reference time is ahead of Target time");
        }
        $this->getInterval();
        $longest_duration = "";
        foreach ($this->interval as $key => $value) {
            if ($value !== 0) {
                switch ($key) {
                    case 'y':
                        $longest_duration = ($value > 1) ? $value . " years ago" : $value . " year ago";
                        break;
                    case 'm':
                        $longest_duration = ($value > 1) ? $value . " months ago" : $value . " month ago";
                        break;
                    case 'd':
                        $longest_duration = ($value > 1) ? $value . " days ago" : $value . " day ago";
                        if ($value >= 7) {
                            $n_week = floor($value / 7);
                            $longest_duration = ($n_week > 1) ? $n_week . " weeks ago" : $n_week . " week ago";
                        }
                        break;
                    case 'h':
                        $longest_duration = ($value > 1) ? $value . " hours ago" : $value . " hour ago";
                        break;
                    case 'i':
                        $longest_duration = ($value > 1) ? $value . " mins ago" : $value . " min ago";
                        break;
                    case 's':
                        $longest_duration = ($value > 1) ? $value . " secs ago" : $value . " sec ago";
                        break;
                    case 'f':
                        $longest_duration = "0 sec ago";
                        break;
                }
                break;
            } else continue;
        }
        return $longest_duration;
    }

    public function getDuration(string $representation = "string")
    {
        if (empty($this->referenceTime)) {
            throw new \RuntimeException("Reference time was not given");
        }
        if (empty($this->targetTime)) {
            throw new \RuntimeException("Target time was not given");
        }
        if (!in_array($representation, ['string', 'array'])) {
            throw new \RuntimeException("Invalid representation parameter given");
        }
        $this->getInterval();
        if ($this->referenceTime < $this->targetTime) {
            if ($representation === 'string') $status = "ago";
            else $status = "past";
        } else {
            if ($representation === 'string') $status = "left";
            else $status = "future";
        }
        $duration = null;
        foreach ($this->interval as $key => $value) {
            if ($value !== 0 and preg_match('/^[ymdhis]$/', $key)) {
                if ($representation === 'string') {
                    $duration .= match ($key) {
                        'y' => ($value > 1) ? $value . " years " : $value . " year ",
                        'm' => ($value > 1) ? $value . " months " : $value . " month ",
                        'd' => ($value > 1) ? $value . " days " : $value . " day ",
                        'h' => ($value > 1) ? $value . " hours " : $value . " hour ",
                        'i' => ($value > 1) ? $value . " mins " : $value . " min ",
                        's' => ($value > 1) ? $value . " secs " : $value . " sec "
                    };
                } else {
                    $duration[$key] = $value;
                }
            } else continue;
        }
        if ($representation === 'string') $duration .= $status;
        else $duration['status'] = $status;
        return $duration;
    }
}

<?php

declare(strict_types=1);

namespace Application\DateTime;

use Application\Membership\MemberStatus;

class ChatTimeDuration extends TimeDuration implements TimeDurationInterface
{
    protected $status;

    public function set_status(MemberStatus $status)
    {
        $this->status = $status->value;
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
                    case 'm':
                        $longest_duration = ($value > 1) ? $value . " months ago" : $value . " month ago";
                        break;
                    case 'd':
                        $longest_duration = ($value > 1) ? $value . " days ago" : $value . " day ago";
                        break;
                    case 'h':
                        $longest_duration = ($value > 1) ? $value . " hours ago" : $value . " hour ago";
                        break;
                    case 'i':
                        $longest_duration = ($value > 1) ? $value . " mins ago" : $value . " min ago";
                        break;
                    case 's':
                        $longest_duration = ($value > 1) ? $value . " secs ago" : $value . " sec ago";
                        if ($value <= 15 && $this->status) {
                            $longest_duration = "Active";
                        }
                        break;
                    case 'f':
                        if ($this->status === 1)
                            $longest_duration = "Active";
                        else
                            $longest_duration = "Offline";
                        break;
                }
                break;
            } else continue;
        }
        return $longest_duration;
    }
}

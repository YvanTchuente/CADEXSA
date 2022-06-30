<?php

declare(strict_types=1);

namespace Application\DateTime;

use Application\Membership\MemberStatus;

class ChatTimeDifference extends Difference implements DifferenceInterface
{
    protected $status;

    public function setStatus(MemberStatus $status)
    {
        $this->status = $status->value;
    }

    protected function ShortForm(\DateInterval $interval)
    {
        $diff = '';
        foreach ($interval as $key => $value) {
            if ($value) {
                switch ($key) {
                    case 'y':
                        $diff = ($value > 1) ? "$value years ago" : "$value year ago";
                        break;
                    case 'm':
                        $diff = ($value > 1) ? "$value months ago" : "$value month ago";
                        break;
                    case 'd':
                        $diff = ($value > 1) ? "$value days ago" : "$value day ago";
                        break;
                    case 'h':
                        $diff = ($value > 1) ? "$value hours ago" : "$value hour ago";
                        break;
                    case 'i':
                        $diff = ($value > 1) ? "$value mins ago" : "$value min ago";
                        break;
                    case 's':
                        $diff = ($value > 1) ? "$value secs ago" : "$value sec ago";
                        if ($value <= 15) {
                            $diff = 'Active';
                        }
                        break;
                    case 'f':
                        $diff = 'now';
                        break;
                }
                break;
            } else {
                continue;
            }
        }
        return $diff;
    }
}

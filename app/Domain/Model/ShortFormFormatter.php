<?php

declare(strict_types=1);

namespace Cadexsa\Domain\Model;

class ShortFormFormatter implements IntervalFormatter
{
    public function format(\DateInterval $interval): string
    {
        $formattedInterval = '';
        foreach ($interval as $key => $value) {
            if ($value) {
                switch ($key) {
                    case 'y':
                        $formattedInterval = ($value > 1) ? "$value years" : "$value year";
                        break;
                    case 'm':
                        $formattedInterval = ($value > 1) ? "$value months" : "$value month";
                        break;
                    case 'd':
                        $formattedInterval = ($value > 1) ? "$value days" : "$value day";
                        if ($value >= 7) {
                            $week = floor($value / 7);
                            $formattedInterval = ($week > 1) ? "$week weeks" : "$week week";
                        }
                        break;
                    case 'h':
                        $formattedInterval = ($value > 1) ? "$value hours" : "$value hour";
                        break;
                    case 'i':
                        $formattedInterval = ($value > 1) ? "$value minutes" : "$value minute";
                        break;
                    case 's':
                        $formattedInterval = ($value > 1) ? "$value seconds" : "$value second";
                        break;
                    case 'f':
                        $formattedInterval = '0 sec';
                        break;
                }
                break;
            } else {
                continue;
            }
        }
        $status = ($interval->invert) ? "ago" : "left";
        $formattedInterval .= " $status";
        return $formattedInterval;
    }
}

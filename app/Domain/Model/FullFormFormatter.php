<?php

declare(strict_types=1);

namespace Cadexsa\Domain\Model;

class FullFormFormatter implements IntervalFormatter
{
    public function format(\DateInterval $interval): string
    {
        $formattedInterval = '';
        foreach ($interval as $key => $value) {
            if ($value and preg_match('/^[ymdhisf]$/', $key)) {
                $formattedInterval .= match ($key) {
                    'y' => ($value > 1) ? "$value years " : "$value year ",
                    'm' => ($value > 1) ? "$value months " : "$value month ",
                    'd' => ($value > 1) ? "$value days " : "$value day ",
                    'h' => ($value > 1) ? "$value hours " : "$value hour ",
                    'i' => ($value > 1) ? "$value minute " : "$value minutes ",
                    's' => ($value > 1) ? "$value second " : "$value seconds ",
                    'f' => "$value second "
                };
                if (preg_match('/(\d+) seconds? (\d+\.\d+) second/', $formattedInterval, $matches)) {
                    $sec = $matches[1];
                    $microsec = $matches[2];
                    $sec += $microsec;
                    $formattedInterval = preg_replace('/' . preg_quote($matches[0]) . '/', "$sec seconds", $formattedInterval);
                }
            } else {
                continue;
            }
        }
        $status = ($interval->invert) ? "ago" : "left";
        $formattedInterval .= " $status";
        return $formattedInterval;
    }
}

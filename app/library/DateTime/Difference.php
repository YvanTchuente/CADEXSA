<?php

declare(strict_types=1);

namespace Application\DateTime;

class Difference implements DifferenceInterface
{
    /**
     * Reference time
     *
     * @var \DateTimeInterface|null
     */
    protected $reference;

    /**
     * Target time
     *
     * @var \DateTimeInterface|null
     */
    protected $target;

    public function __construct(?\DateTimeInterface $reference = null, ?\DateTimeInterface $target = null)
    {
        $this->reference = $reference;
        $this->target = $target;
    }

    public function setReferenceTime(\DateTimeInterface $time)
    {
        $this->reference = $time;
    }

    public function setTargetTime(\DateTimeInterface $time)
    {
        $this->target = $time;
    }

    protected function computeDifference()
    {
        return $this->target->diff($this->reference);
    }

    public function diff(string $form = 'short')
    {
        if (!in_array($form, ['short', 'full'])) {
            throw new \DomainException('Unknown form');
        }
        $this->isOk();
        $diff = $this->computeDifference();
        $diff = match ($form) {
            'short' => $this->ShortForm($diff),
            'full' => $this->FullForm($diff),
        };
        return $diff;
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
                        if ($value >= 7) {
                            $n_week = floor($value / 7);
                            $diff = ($n_week > 1) ? "$n_week weeks ago" : "$n_week week ago";
                        }
                        break;
                    case 'h':
                        $diff = ($value > 1) ? "$value hours ago" : "$value hour ago";
                        break;
                    case 'i':
                        $diff = ($value > 1) ? "$value minutes ago" : "$value minute ago";
                        break;
                    case 's':
                        $diff = ($value > 1) ? "$value seconds ago" : "$value second ago";
                        break;
                    case 'f':
                        $diff = '0 sec ago';
                        break;
                }
                break;
            } else {
                continue;
            }
        }
        return $diff;
    }

    protected function FullForm(\DateInterval $interval)
    {
        $status = match (true) {
            ($this->reference < $this->target) => 'left',
            ($this->reference > $this->target) => 'ago'
        };
        $diff = '';
        foreach ($interval as $key => $value) {
            if ($value and preg_match('/^[ymdhisf]$/', $key)) {
                $diff .= match ($key) {
                    'y' => ($value > 1) ? "$value years " : "$value year ",
                    'm' => ($value > 1) ? "$value months " : "$value month ",
                    'd' => ($value > 1) ? "$value days " : "$value day ",
                    'h' => ($value > 1) ? "$value hours " : "$value hour ",
                    'i' => ($value > 1) ? "$value minute " : "$value minutes ",
                    's' => ($value > 1) ? "$value second " : "$value seconds ",
                    'f' => "$value second "
                };
                if (preg_match('/(\d+) seconds? (\d+\.\d+) second/', $diff, $matches)) {
                    $sec = $matches[1];
                    $microsec = $matches[2];
                    $sec += $microsec;
                    $diff = preg_replace('/' . preg_quote($matches[0]) . '/', "$sec seconds", $diff);
                }
            } else {
                continue;
            }
        }
        $diff .= $status;
        return $diff;
    }

    protected function isOk()
    {
        if (empty($this->reference)) {
            throw new \RuntimeException('The reference time was not given');
        }
        if (empty($this->target)) {
            throw new \RuntimeException('The target time was not given');
        }
    }
}

<?php

declare(strict_types=1);

namespace Cadexsa\Domain\Model\ExStudent;

enum Orientation: string
{
    case ARTS = "Arts";
    case SCIENCE = "Science";
    case COMMERCIAL = "Commercial";

    public function label()
    {
        return $this->name;
    }
}

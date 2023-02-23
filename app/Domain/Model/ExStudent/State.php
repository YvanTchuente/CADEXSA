<?php

declare(strict_types=1);

namespace Cadexsa\Domain\Model\ExStudent;

enum State: int
{
    case ONLINE = 1;
    case OFFLINE = 0;

    public function label()
    {
        return ucfirst(strtolower($this->name));
    }
}

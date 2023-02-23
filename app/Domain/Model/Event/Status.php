<?php

namespace Cadexsa\Domain\Model\Event;

enum Status: int
{
    case UPCOMING = 0;
    case OCCURRED = 1;
}

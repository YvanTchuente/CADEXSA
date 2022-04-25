<?php

namespace Application\CMS\Events;

enum EventStatus: int
{
    case HAPPENED = 1;
    case NOT_HAPPENED = 0;
}

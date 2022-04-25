<?php

declare(strict_types=1);

namespace Application\Membership;

enum MemberStatus: int
{
    case ONLINE = 1;
    case OFFLINE = 0;
}

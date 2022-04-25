<?php

declare(strict_types=1);

namespace Application\Membership;

enum MemberLevel: int
{
    case ADMINISTRATOR = 1;
    case EDITOR = 2;
    case REGULAR = 3;
}

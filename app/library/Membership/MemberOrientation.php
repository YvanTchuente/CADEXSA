<?php

declare(strict_types=1);

namespace Application\Membership;

enum MemberOrientation: string
{
    case ARTS = "Arts";
    case SCIENCE = "Science";
    case COMMERCIAL = "Commercial";
}

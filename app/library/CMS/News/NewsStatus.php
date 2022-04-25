<?php

namespace Application\CMS\News;

enum NewsStatus: int
{
    case PUBLISHED = 1;
    case NOT_PUBLISHED = 0;
}

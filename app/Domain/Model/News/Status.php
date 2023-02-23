<?php

namespace Cadexsa\Domain\Model\News;

/**
 * Represents the status of a news article.
 */
enum Status: int
{
    case UNPUBLISHED = 0;
    case PUBLISHED = 1;
}

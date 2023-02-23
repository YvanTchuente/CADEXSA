<?php

declare(strict_types=1);

namespace Cadexsa\Domain\Model\News;

/**
 * Represents a tag used to categorize news articles.
 */
enum Tag: string
{
    case ALUMNI = "alumni";
    case EVENTS = "events";
    case SCHOOL = "school";
    case MEMBERS = "members";
    case STUDENTS = "students";

    public function label()
    {
        return ucfirst(strtolower($this->name));
    }
}

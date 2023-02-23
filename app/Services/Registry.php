<?php

declare(strict_types=1);

namespace Cadexsa\Services;

use Cadexsa\Services\NewsService;
use Cadexsa\Services\EventService;
use Cadexsa\Services\MessageService;
use Cadexsa\Services\ExStudentService;

/**
 * Registry of application services.
 */
class Registry
{
    public static function newsService()
    {
        return new NewsService;
    }

    public static function eventService()
    {
        return new EventService;
    }

    public static function messageService()
    {
        return new MessageService;
    }

    public static function pictureService()
    {
        return new PictureService;
    }

    public static function exStudentService()
    {
        return new ExStudentService;
    }
}

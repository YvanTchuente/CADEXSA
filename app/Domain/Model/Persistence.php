<?php

declare(strict_types=1);

namespace Cadexsa\Domain\Model;

use Cadexsa\Infrastructure\Persistence\EventRepository;
use Cadexsa\Infrastructure\Persistence\InMemoryStrategy;
use Cadexsa\Infrastructure\Persistence\MessageRepository;
use Cadexsa\Infrastructure\Persistence\PictureRepository;
use Cadexsa\Infrastructure\Persistence\RelationalStrategy;
use Cadexsa\Infrastructure\Persistence\RepositoryStrategy;
use Cadexsa\Infrastructure\Persistence\ExStudentRepository;
use Cadexsa\Infrastructure\Persistence\NewsArticleRepository;

/**
 * Registry of repositories.
 */
class Persistence
{
    private EventRepository $eventRepository;

    private MessageRepository $messageRepository;

    private PictureRepository $pictureRepository;

    private ExStudentRepository $exStudentRepository;

    private NewsArticleRepository $newsArticleRepository;

    private static self $instance;

    private function __construct(RepositoryStrategy $strategy)
    {
        $this->newsArticleRepository = new NewsArticleRepository($strategy);
        $this->eventRepository = new EventRepository($strategy);
        $this->messageRepository = new MessageRepository($strategy);
        $this->pictureRepository = new PictureRepository($strategy);
        $this->exStudentRepository = new ExStudentRepository($strategy);
    }

    private static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new Persistence(new RelationalStrategy);
        }
        return self::$instance;
    }

    public static function testMode()
    {
        self::$instance = new Persistence(new InMemoryStrategy);
    }

    /**
     * Gets the event repository.
     */
    public static function eventRepository()
    {
        return self::getInstance()->eventRepository;
    }

    /**
     * Gets the ex-student repository.
     */
    public static function exStudentRepository()
    {
        return self::getInstance()->exStudentRepository;
    }

    /**
     * Gets the repository messages.
     */
    public static function messageRepository()
    {
        return self::getInstance()->messageRepository;
    }

    /**
     * Gets the gallery picture repository.
     */
    public static function pictureRepository()
    {
        return self::getInstance()->pictureRepository;
    }

    /**
     * Gets the news article repository.
     */
    public static function newsArticleRepository()
    {
        return self::getInstance()->newsArticleRepository;
    }
}

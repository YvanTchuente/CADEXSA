<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure\Persistence;

use Cadexsa\Domain\Model\Event\Event;
use Cadexsa\Infrastructure\Encrypter;
use Cadexsa\Domain\Model\Message\Message;
use Cadexsa\Domain\Model\Picture\Picture;
use Cadexsa\Domain\Model\News\NewsArticle;
use Cadexsa\Domain\Model\ExStudent\ExStudent;

class MapperRegistry
{
    /**
     * @var Mapper[]
     */
    private static array $mappers = [];

    private static function getMappers()
    {
        if (!empty(static::$mappers)) {
            return static::$mappers;
        }

        self::$mappers[Event::class] = new EventMapper;
        self::$mappers[Picture::class] = new PictureMapper;
        self::$mappers[NewsArticle::class] = new NewsMapper;
        self::$mappers[ExStudent::class] = new ExStudentMapper;
        self::$mappers[Message::class] = new MessageMapper(new Encrypter, new Encrypter);

        return static::$mappers;
    }

    /**
     * Retrieves the mapper for a given class of entities.
     *
     * @param string $class The class name of an entity.
     * 
     * @throws \RuntimeException If there is no mapper.
     */
    public static function getMapper(string $class)
    {
        $mappers = static::getMappers();
        if (!isset($mappers[$class])) {
            throw new \RuntimeException("There is no registered mapper for '$class' entities");
        }

        return $mappers[$class];
    }
}

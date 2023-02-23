<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure\Persistence;

use Cadexsa\Domain\Model\News\NewsArticle;
use Cadexsa\Domain\Factories\NewsArticleFactory;

/**
 * News data mapper
 */
class NewsMapper extends Mapper
{
    protected function getEntityName(): string
    {
        return 'NewsArticle';
    }

    protected function doLoad(array $resultSet)
    {
        $newsFactory = new NewsArticleFactory($this->dataMap);
        $newsArticle = $newsFactory->reconstitute($resultSet);
        return $newsArticle;
    }

    /**
     * @param NewsArticle $entity
     */
    protected function doValidateContent($entity)
    {
        switch (true) {
            case (!$entity->getBody()):
                throw new \DomainException('Invalid article body.');
                break;
            case (!$entity->getTitle()):
                throw new \DomainException('Invalid article title.');
                break;
            case (!$entity->getStatus()):
                throw new \DomainException("Invalid article status.");
                break;
            case (!$entity->getImage()):
                throw new \DomainException('Invalid article image.');
                break;
            case (!$entity->getPublicationDate()):
                throw new \DomainException("Invalid article publication date.");
                break;
        }
    }
}

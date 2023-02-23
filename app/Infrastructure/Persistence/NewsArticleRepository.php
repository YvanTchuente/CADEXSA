<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure\Persistence;

use Cadexsa\Domain\Model\News\NewsArticle;
use Cadexsa\Infrastructure\Persistence\Criteria;
use Cadexsa\Domain\Model\News\MissingNewsArticle;
use Cadexsa\Infrastructure\Persistence\Contracts\Criteria as CriteriaContract;

class NewsArticleRepository extends Repository
{
    /**
     * Finds a news article by its identifier.
     *
     * @param integer $newsArticleId The identifier of a news article.
     * @return NewsArticle
     */
    public function findById(int $newsArticleId): NewsArticle
    {
        $criteria = Criteria::equal('id', $newsArticleId);
        $newsArticle = $this->selectMatch($criteria) ?? new MissingNewsArticle;
        return $newsArticle;
    }

    /**
     * Selects the first news article matching a given criteria.
     *
     * @param Criteria $criteria A selection criteria.
     * @return NewsArticle The news article.
     */
    public function selectMatch(CriteriaContract $criteria): NewsArticle
    {
        return $this->strategy->selectMatching($criteria, $this)[0] ?? new MissingNewsArticle;
    }

    /**
     * Selects news articles matching a given criteria.
     * 
     * @param Criteria $criteria A selection criteria.
     * @return NewsArticle[] A collection of news articles.
     */
    public function selectMatching(CriteriaContract $criteria): array
    {
        return $this->strategy->selectMatching($criteria, $this);
    }

    /**
     * Adds a news article to the repository.
     *
     * @param NewsArticle $newsArticle The news article.
     */
    public function add(NewsArticle $newsArticle)
    {
        $this->strategy->add($newsArticle);
    }

    /**
     * Removes a news article from the repository.
     *
     * @param NewsArticle $newsArticle The news article.
     */
    public function remove(NewsArticle $newsArticle)
    {
        $this->strategy->remove($newsArticle);
    }

    /**
     * Retrieves all news articles.
     * 
     * @return NewsArticle[] news articles.
     */
    public function all(): array
    {
        return $this->strategy->all($this);
    }

    public function getEntityClass(): string
    {
        return MapperRegistry::getMapper(NewsArticle::class)->getDataMap()->getEntityClass();
    }
}

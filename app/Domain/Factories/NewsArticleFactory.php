<?php

declare(strict_types=1);

namespace Cadexsa\Domain\Factories;

use Cadexsa\Domain\Model\News\Tag;
use Cadexsa\Domain\Model\News\Status;
use Cadexsa\Domain\Model\News\NewsArticle;

class NewsArticleFactory extends EntityFactory
{
    /**
     * Creates a news article.
     *
     * @param string $title The article's title
     * @param string $body The article's body 
     * @param Tag[] $tags The article's tags 
     * @param string $image The URI of a representative image of the article
     * @param integer $authorId The identifier of the article's author
     * @param Status $status The article's status
     */
    public static function create(
        string $title,
        string $body,
        array $tags,
        string $image,
        int $authorId,
        Status $status = Status::UNPUBLISHED
    ): NewsArticle {
        $id = app()->IdGenerator()->generateId();
        $newsArticle = new NewsArticle($id, $title, $body, $tags, $image, $authorId, $status);

        return $newsArticle;
    }

    /**
     * Reconstitutes a news article from its stored representation.
     * 
     * @param array $resultSet An associative array of record data.
     */
    public function reconstitute(array $resultSet): NewsArticle
    {
        $this->validateResults($resultSet);
        $status = Status::from((int) $resultSet['status']);
        foreach (preg_split("/;\s?/", $resultSet['tags']) as $tag) {
            $tags[] = Tag::from(strtolower($tag));
        }

        // Reconstitute
        $newsArticle = new NewsArticle((int) $resultSet['id'], $resultSet['title'], $resultSet['body'], $tags, $resultSet['image'],  $resultSet['author_id'], $status, $resultSet['published_on']);

        return $newsArticle;
    }
}

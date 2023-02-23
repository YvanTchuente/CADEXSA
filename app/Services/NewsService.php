<?php

declare(strict_types=1);

namespace Cadexsa\Services;

use Cadexsa\Presentation\View;
use Cadexsa\Domain\ServiceRegistry;
use Cadexsa\Domain\Model\News\Tag;
use Cadexsa\Domain\Model\News\Status;
use Cadexsa\Domain\Model\Persistence;
use Cadexsa\Domain\Model\News\NewsArticle;
use Cadexsa\Domain\Factories\NewsArticleFactory;
use Cadexsa\Infrastructure\Messaging\NewsletterService;

class NewsService
{
    /**
     * Creates and saves a news article.
     *
     * @param string $title The article's title
     * @param string $body The article's body 
     * @param string[] $tagList The article's tags 
     * @param string $image The URI of a representative image of the article
     * @param integer $authorId The identifier of the article's author
     * @param Status $status The article's status
     * 
     * @return NewsArticle The news article.
     */
    public function createNewsArticle(
        string $title,
        string $body,
        array $tagList,
        string $image,
        int $authorId
    ): NewsArticle {
        $tags = $this->getTags($tagList);
        $newsArticle = NewsArticleFactory::create($title, $body, $tags, $image, $authorId);
        Persistence::newsArticleRepository()->add($newsArticle);
        return $newsArticle;
    }

    /**
     * Creates and publishes a news article.
     *
     * @param string $title The article's title 
     * @param string $body The article's body
     * @param string[] $tagList The article's tags 
     * @param string $image The URI of a representative image of the article
     * @param integer $authorId The identifier of the article's author
     * 
     * @return NewsArticle The news article.
     */
    public function publishNewsArticle(
        string $title,
        string $body,
        array $tagList,
        string $image,
        int $authorId
    ): NewsArticle {
        $tags = $this->getTags($tagList);
        $newsArticle = NewsArticleFactory::create($title, $body, $tags, $image, $authorId, Status::PUBLISHED);
        Persistence::newsArticleRepository()->add($newsArticle);
        $timeElapsed = ServiceRegistry::timeIntervalCalculator()->interval($newsArticle->getPublicationDate(), new \DateTime);
        $summary = $newsArticle->getBody(true);
        $host = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'];
        $link = $host . "/news/" . urlencode(strtolower($title));
        $params = ['host' => $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'], 'id' => $newsArticle->getId(), 'title' => $newsArticle->getTitle(), 'body' => $summary, 'image' => $newsArticle->getImage(), 'timeElapsed' => $timeElapsed, 'link' => $link];
        $new_article_email_view = new View(views_path('emails/new_article_email'), $params);
        $newsletterService = new NewsletterService;
        try {
            $newsletterService->broadcastNewsletter(
                $new_article_email_view->render(),
                $newsArticle->getTitle(),
                'Cadexsa news alert'
            );
        } catch (\Throwable $e) {
            // Do nothing
        }
        return $newsArticle;
    }

    private function getTags(array $tagList): array
    {
        try {
            foreach ($tagList as $tag) {
                $tags[] = Tag::from(strtolower($tag));
            }
            return $tags;
        } catch (\Throwable $e) {
            preg_match("/^\"(\w+)\"/", $e->getMessage(), $matches);
            $tag = $matches[1];
            throw new \RuntimeException("$tag is not a valid tag");
        }
    }
}

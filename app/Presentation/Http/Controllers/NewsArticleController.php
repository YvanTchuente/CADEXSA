<?php

declare(strict_types=1);

namespace Cadexsa\Presentation\Http\Controllers;

use Cadexsa\Presentation\View;
use Cadexsa\Domain\Model\INull;
use Cadexsa\Domain\Model\News\Tag;
use Cadexsa\Domain\Model\Persistence;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Cadexsa\Infrastructure\Persistence\Criteria;
use Cadexsa\Infrastructure\Persistence\Paginator;
use Cadexsa\Domain\Exceptions\ModelNotFoundException;

class NewsArticleController extends Controller
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $title = str_replace("+", " ", urldecode($request->getQueryParams()['title']));
        $newsArticle = Persistence::newsArticleRepository()->selectMatch(Criteria::matches('title', $title));
        if ($newsArticle instanceof INull) {
            throw new ModelNotFoundException;
        }
        $title = $newsArticle->getTitle();
        $publication = array(
            'date' => $newsArticle->getPublicationDate()->format("l, j F Y"),
            'time' => $newsArticle->getPublicationDate()->format("g:i a")
        );
        $body = $newsArticle->getBody();
        $image = $newsArticle->getImage();
        foreach ($newsArticle->getTags() as $tag) {
            $tags[] = $tag->label();
        }
        $author = $newsArticle->getAuthor();
        $authorUserName = $author->getUsername();
        $authorName = $author->getName();
        $authorPicture = $author->getAvatar();
        // Fetch 5 most recent articles
        $recentNews = [];
        $paginator = new Paginator(Persistence::newsArticleRepository()->all(), 5);
        foreach ($paginator->getBatch(1) as $recentNewsArticle) {
            if ($newsArticle->getId() == $recentNewsArticle->getId()) {
                continue;
            }
            $recentNews[] = $recentNewsArticle;
        }
        foreach (Tag::cases() as $tag) {
            $all_tags[] = $tag->label();
        }
        $view_params = [
            'title' => $title,
            'publication' => $publication,
            'body' => $body,
            'image' => $image,
            'tags' => $tags,
            'authorUserName' => $authorUserName,
            'authorName' => $authorName,
            'authorPicture' => $authorPicture,
            'all_tags' => $all_tags,
            'recentNews' => $recentNews
        ];
        return $this->prepareResponseFromView(new View(views_path("news_article.php"), $view_params));
    }
}

<?php

declare(strict_types=1);

namespace Cadexsa\Presentation\Http\Controllers;

use Cadexsa\Presentation\View;
use Cadexsa\Domain\Model\News\Tag;
use Cadexsa\Domain\Model\Persistence;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Cadexsa\Infrastructure\Persistence\Criteria;
use Cadexsa\Infrastructure\Persistence\Paginator;
use Cadexsa\Infrastructure\Persistence\ConjunctionCriteria;

class NewsFiltrationController extends Controller
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $params = $request->getQueryParams();
        $timestamp = new \DateTime($params['month'] . " " . $params['year']);
        $tag = Tag::from($params['tag']);
        $page = (int) ($params['page'] ?? 1);

        try {
            $criteria = new ConjunctionCriteria(Criteria::equal('tags', $tag->value), Criteria::lessThan('publishedOn', $timestamp->format("Y-m-d")));
            $filteredNews = Persistence::newsArticleRepository()->selectMatching($criteria);
            $paginator = new Paginator($filteredNews, 8);
            $news = $paginator->getBatch($page);
            $summaries = [];
            foreach ($news as $newsArticle) {
                $summary = $newsArticle->getBody(true);
                $summary['link'] = "/news/" . urlencode(strtolower($newsArticle->getTitle()));
                $summaries[] = $summary;
            }
            foreach (Tag::cases() as $tag) {
                $tags[] = $tag->label();
            }
            $view_params = ['summaries' => $summaries, 'tags' => $tags, 'pageCount' => $paginator->batchCount, 'page' => $page];
        } catch (\Throwable $e) {
            $view_params['msg'] = "There are no matching news articles.";
        }

        $view_params['years'] = range((int) date('Y'), 2019);

        return $this->prepareResponseFromView(new View(views_path("news.php"), $view_params));
    }
}

<?php

declare(strict_types=1);

namespace Cadexsa\Presentation\Http\Controllers;

use Cadexsa\Presentation\View;
use Cadexsa\Services\Registry;
use Cadexsa\Domain\Model\News\Tag;
use Cadexsa\Domain\Model\Persistence;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Cadexsa\Infrastructure\Persistence\Paginator;
use Cadexsa\Infrastructure\Transaction\TransactionManager;

class NewsPublicationController extends Controller
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $method = strtoupper($request->getMethod());

        if ($method == "POST") {
            $action = $request->getParsedBody()['action'];
            $title = $request->getParsedBody()['title'];
            $body = $request->getParsedBody()['body'];
            $image = $request->getParsedBody()['image'];
            $authorId = (int) $request->getParsedBody()['authorId'];
            $tags = preg_split("/;\s?/", $request->getParsedBody()['tags']);

            if (preg_match('/\/articles_thumbnails\//', $image)) {
                // In case the user uploaded a picture as thumbnail
                $oldName = sha1(user()->getUsername() . user()->getId()) . ".jpg";
                $path = public_path("/images/articles_thumbnails/");
                $newName = sha1(file_get_contents($path . $oldName)) . ".jpg";
                if (rename($path . $oldName, $path . $newName)) {
                    $image = "/images/articles_thumbnails/$newName";
                }
            }

            $image = (string) $request->getUri()->withPath($image);
            try {
                switch ($action) {
                    case 'create':
                        Registry::newsService()->createNewsArticle($title, $body, $tags, $image, $authorId);
                        $view_params['success_message'] = "Article created and saved for future edits and/or publication";
                        break;
                    case 'publish':
                        $newsArticle = Registry::newsService()->publishNewsArticle($title, $body, $tags, $image, $authorId);
                        TransactionManager::commit();
                        header('Location: /news/' . urlencode(strtolower($newsArticle->getTitle())));
                        exit();
                        break;
                }
            } catch (\Throwable $e) {
                $view_params['message'] = "An error occurred during the creation process";
            }
        }

        try {
            $pictures = (new Paginator(Persistence::pictureRepository()->all(), 8))->getBatch(1);
            $view_params['pictures'] = $pictures;
        } catch (\Throwable $e) {
        }

        $_SESSION['token'] = $view_params['token'] = csrf_token();
        foreach (Tag::cases() as $tag) $view_params['tags'][] = $tag->label();

        return $this->prepareResponseFromView(new View(views_path("news_publisher.php"), $view_params));
    }
}

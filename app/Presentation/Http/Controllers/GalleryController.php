<?php

declare(strict_types=1);

namespace Cadexsa\Presentation\Http\Controllers;

use Cadexsa\Presentation\View;
use Cadexsa\Domain\Model\Persistence;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Cadexsa\Infrastructure\Persistence\Paginator;

class GalleryController extends Controller
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $params = $request->getQueryParams();
        $page = (int) ($params['page'] ?? 1);

        try {
            $paginator = new Paginator(Persistence::pictureRepository()->all(), 8);
            $pictures = $paginator->getBatch($page);
        } catch (\Exception $e) {
            header("Location: /");
            exit();
        }

        $years = range((int) date('Y'), 2019);
        $view_params = ['pictures' => $pictures, 'years' => $years, 'pageCount' => $paginator->batchCount, 'page' => $page];

        return $this->prepareResponseFromView(new View(views_path("gallery.php"), $view_params));
    }
}

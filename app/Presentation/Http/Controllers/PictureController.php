<?php

declare(strict_types=1);

namespace Cadexsa\Presentation\Http\Controllers;

use Cadexsa\Presentation\View;
use Cadexsa\Domain\Model\Persistence;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class PictureController extends Controller
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $pictureId = (int) $request->getQueryParams()['id'];
        $picture = Persistence::pictureRepository()->findById($pictureId);
        $view_params = ['picture' => $picture];

        return $this->prepareResponseFromView(new View(views_path("picture.php"), $view_params));
    }
}

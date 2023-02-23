<?php

declare(strict_types=1);

namespace Cadexsa\Presentation\Http\Controllers;

use Cadexsa\Presentation\View;
use Cadexsa\Services\Registry;
use Cadexsa\Domain\Model\Persistence;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Cadexsa\Services\InvalidPictureException;
use Cadexsa\Infrastructure\Transaction\TransactionManager;

class GalleryPictureUploadController extends Controller
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $method = strtolower($request->getMethod());
        if ($method == "post") {
            $picture = $request->getUploadedFiles()['picture'];
            $description = $request->getParsedBody()['description'];
            $shotOn = implode(" ", [$request->getParsedBody()['shotOn']]);

            try {
                $picture = Registry::pictureService()->storePicture($picture, $shotOn, $description);
                TransactionManager::commit();

                // Log the event
                app()->getLogger()->info("{exstudent} has uploaded a new picture for the gallery. The picture is at {location}", ['exstudent' => (string) user()->getName(), 'location' => public_path($picture->getLocation())]);
                header('Location: /gallery/pictures/' . $picture->getId());
                exit();
            } catch (\Exception $e) {
                if ($e instanceof InvalidPictureException) {
                    $view_params['message'] = $e->getMessage();
                } else {
                    $view_params['message'] = "We encountered an error during the upload.";
                }
            }
        }
        $_SESSION['token'] = $view_params['token'] = csrf_token();
        $publisher_view = new View(views_path("picture_uploader.php"), $view_params);
        return $this->prepareResponseFromView($publisher_view);
    }
}

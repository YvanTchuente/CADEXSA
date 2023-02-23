<?php

declare(strict_types=1);

namespace Cadexsa\Presentation\Http\Controllers;

use Cadexsa\Presentation\View;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ContentRecoveryController extends Controller
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $params = $request->getQueryParams();

        try {
            if (!$params) {
                throw new \RuntimeException;
            }

            $cms_deletion_history_filename = resource_path("/temporary/cms_deleted_items");
            $cms_deletion_history_file = $this->streamFactory->createStreamFromFile($cms_deletion_history_filename, "r+");
            $cms_deletion_history = (string) $cms_deletion_history_file;

            if (!$cms_deletion_history) {
                throw new \RuntimeException;
            }

            $level = (int) $params['l'] - 1;
            $caretaker = unserialize($cms_deletion_history);
            $memento = $caretaker->getHistory()[$level];
            $originator = $memento->originator();
            $deleteCommand = new $originator;
            $caretaker->restore($deleteCommand, $level);
            $deleteCommand->undo(); // Recover the deletion

            // Update the history file
            if (!$caretaker->getHistory()) {
                unlink($cms_deletion_history_filename);
                unset($cms_deletion_history);
            } else {
                $cms_deletion_history = serialize($caretaker);
                $cms_deletion_history_file->rewind(); // Reset pointer to beginning of the file
                $cms_deletion_history_file->write($cms_deletion_history);
            }
        } catch (\Throwable $e) {
            header('Location: /cms/delete');
        }

        $view_params['mementoName'] = $memento->getName();
        return $this->prepareResponseFromView(new View(views_path("recovered_deletion.php"), $view_params));
    }
}

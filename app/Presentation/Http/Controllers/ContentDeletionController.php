<?php

declare(strict_types=1);

namespace Cadexsa\Presentation\Http\Controllers;

use Cadexsa\Domain\Caretaker;
use Cadexsa\Presentation\View;
use Cadexsa\Domain\DeleteNewsCommand;
use Cadexsa\Domain\DeleteEventCommand;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ContentDeletionController extends Controller
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $cms_deletion_history_filename = resource_path("/temporary/cms_deleted_items");

        if ($request->getQueryParams()) {
            $params = $request->getQueryParams();

            if (!file_exists($cms_deletion_history_filename)) {
                touch($cms_deletion_history_filename);
            }

            try {
                $cms_deletion_history_file = $this->streamFactory->createStreamFromFile($cms_deletion_history_filename, "r+");
                $cms_deletion_history = (string) $cms_deletion_history_file;

                switch (true) {
                    case (isset($params['type']) && isset($params['id'])):
                        if ($cms_deletion_history) {
                            $caretaker = unserialize($cms_deletion_history);
                        } else {
                            $caretaker = new Caretaker;
                        }
                        switch ($params['type']) {
                            case 'event':
                                $deleteCommand = new DeleteEventCommand((int) $params['id']);
                                break;
                            case 'news':
                                $deleteCommand = new DeleteNewsCommand((int) $params['id']);
                                break;
                            default:
                                throw new \DomainException("Unknown type.");
                                break;
                        }
                        $caretaker->backup($deleteCommand);
                        // Serialize and store the caretaker
                        $cms_deletion_history = serialize($caretaker);
                        $cms_deletion_history_file->rewind();
                        $cms_deletion_history_file->write($cms_deletion_history);
                        // Execute the deletion
                        $deleteCommand->execute();
                        $params['deleted'] = true;
                        break;

                    case (isset($params['memento'])):
                        if (!$cms_deletion_history) {
                            throw new \RuntimeException;
                        }
                        $name = urldecode($params['memento']);
                        $caretaker = unserialize($cms_deletion_history);
                        $caretaker->deleteMemento($name);
                        // Update the history file
                        if (!$caretaker->getHistory()) {
                            unlink($cms_deletion_history_filename);
                            unset($cms_deletion_history);
                        } else {
                            $cms_deletion_history = serialize($caretaker);
                            $cms_deletion_history_file->rewind(); // Reset pointer to beginning of the file
                            $cms_deletion_history_file->write($cms_deletion_history);
                        }
                        break;
                }

                if ($caretaker->getHistory()) {
                    $view_params['mementos'] = $caretaker->getHistory();
                }
            } catch (\Throwable $e) {
                header('Location: /cms/');
                exit();
            }
        } else {
            try {
                $cms_deletion_history_file = $this->streamFactory->createStreamFromFile($cms_deletion_history_filename, "r");
                $cms_deletion_history = (string) $cms_deletion_history_file;

                if ($cms_deletion_history) {
                    $caretaker = unserialize($cms_deletion_history);
                    $view_params['mementos'] = $caretaker->getHistory();
                }
            } catch (\Throwable $e) {
                header('Location: /cms/');
                exit();
            }
        }

        if (isset($view_params['mementos'])) {
            for ($i = 0; $i < count($view_params['mementos']); $i++) {
                $memento = $view_params['mementos'][$i];
                $level = $i + 1;
                $name = $memento->getName();
                $date = $memento->getDate();
                $mementos[] = ['name' => $name, 'date' => $date, 'level' => $level];
            }
            $view_params['mementos'] = $mementos;
        }

        return $this->prepareResponseFromView(new View(views_path("cms_deleter.php"), $view_params));
    }
}

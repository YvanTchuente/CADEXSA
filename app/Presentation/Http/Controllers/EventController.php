<?php

declare(strict_types=1);

namespace Cadexsa\Presentation\Http\Controllers;

use Cadexsa\Presentation\View;
use Cadexsa\Domain\Model\INull;
use Cadexsa\Domain\Model\Persistence;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Cadexsa\Infrastructure\Persistence\Criteria;
use Cadexsa\Domain\Exceptions\ModelNotFoundException;

class EventController extends Controller
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        $event_name = str_replace("+", " ", urldecode($queryParams['name']));
        $event = Persistence::eventRepository()->selectMatch(Criteria::matches('name', $event_name));

        if ($event instanceof INull) {
            throw new ModelNotFoundException;
        }

        // Determine whether the ex-student requesting this page has confirmed his/her participation
        $get_participation_status_stmt = app()->database->getConnection()->pdo->prepare("SELECT COUNT(*) as participation_status FROM event_participants WHERE event_id = ? AND exstudent_id = ?");
        $get_participation_status_stmt->execute([$event->getId(), user()->getId()]);
        $exstudent_participate = boolval($get_participation_status_stmt->fetch(\PDO::FETCH_ASSOC)['participation_status']);
        if ($exstudent_participate) {
            $view_params['message'] = "Your participation has been confirmed";
        }

        // Retrieve the event's participants
        $participants = [];
        $get_participants_stmt = app()->database->getConnection()->pdo->prepare("SELECT exstudent_id, attended FROM event_participants WHERE event_id = ?");
        $get_participants_stmt->execute([$event->getId()]);
        foreach ($get_participants_stmt->fetchAll(\PDO::FETCH_ASSOC) as $participant) {
            $participants[] = Persistence::exStudentRepository()->findById($participant['exstudent_id']);
        }
        $view_params['participants'] = $participants;

        if (isset($queryParams['action']) and $queryParams['action'] == "participate" and !$exstudent_participate) {
            $connection = app()->database->getConnection();
            try {
                $connection->pdo->beginTransaction();
                $insert_stmt = $connection->pdo->prepare("INSERT event_participants (event_id, exstudent_id) VALUES (:event_id, :exstudent_id)");
                $insert_stmt->bindValue("event_id", $event->getId());
                $insert_stmt->bindValue("exstudent_id", user()->getId());
                $insert_stmt->execute();
                $connection->pdo->commit();
                $view_params['message'] = "Your participation has been confirmed";
            } catch (\PDOException $e) {
                $connection->pdo->rollBack();
            }
        }

        $view_params += [
            'name' => $event->getName(),
            'description' => $event->getDescription(),
            'venue' => $event->getVenue(),
            'image' => $event->getImage(),
            'occursOn' => $event->getOccurrenceDate()
        ];

        return $this->prepareResponseFromView(new View(views_path("event.php"), $view_params));
    }
}

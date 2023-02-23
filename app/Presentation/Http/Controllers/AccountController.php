<?php

declare(strict_types=1);

namespace Cadexsa\Presentation\Http\Controllers;

use Cadexsa\Presentation\View;
use Cadexsa\Domain\Model\INull;
use Cadexsa\Domain\ServiceRegistry;
use Cadexsa\Domain\Model\Persistence;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Cadexsa\Infrastructure\Persistence\Criteria;
use Cadexsa\Infrastructure\Persistence\Paginator;
use Cadexsa\Domain\Exceptions\AuthenticationException;

class AccountController extends Controller
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $params = $request->getQueryParams();
        $exstudent = Persistence::exStudentRepository()->selectMatch(Criteria::equal("username", $params['username']));

        if ($exstudent instanceof INull) {
            throw new AuthenticationException("The exstudent is missing.", route('login'));
        }

        if ($exstudent->getId() !== user()->getId()) {
            $guest = true;
        }

        foreach ((new Paginator(Persistence::exStudentRepository()->all(), 6))->getBatch(1) as $person) {
            if ($exstudent->getId() == $person->getId()) {
                continue;
            }

            $lastSeen = ServiceRegistry::timeIntervalCalculator()->elapsedTimeSinceLastActivity($person->getId());
            $chat_users[] = ['id' => $person->getId(), 'name' => $person->getName(), 'avatar' => $person->getAvatar(), 'state' => $person->state()->label(), 'lastSeen' => $lastSeen];
        }

        $view_params = [
            'exstudent' => $exstudent,
            'chat_users' => $chat_users ?? [],
            'guest' => $guest ?? false
        ];

        return $this->prepareResponseFromView(new View(views_path("account.php"), $view_params));
    }
}

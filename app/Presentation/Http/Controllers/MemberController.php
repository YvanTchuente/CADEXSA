<?php

declare(strict_types=1);

namespace Cadexsa\Presentation\Http\Controllers;

use Cadexsa\Domain\ServiceRegistry;
use Cadexsa\Domain\Model\Persistence;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Cadexsa\Infrastructure\Persistence\Criteria;

class MemberController extends Controller
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $username = $request->getQueryParams()['username'];
        $exstudent = Persistence::exStudentRepository()->selectMatch(Criteria::equal('username', $username));

        $exstudent_data = $exstudent->toArray();
        $exstudent_data['state'] = ServiceRegistry::authenticationService()->check($exstudent_data['username']) ? "online" : "offline";
        $exstudent_data = json_encode($exstudent_data);
        $response = $this->prepareResponse($exstudent_data);

        return $response;
    }
}

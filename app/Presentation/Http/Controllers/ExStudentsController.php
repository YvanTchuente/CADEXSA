<?php

declare(strict_types=1);

namespace Cadexsa\Presentation\Http\Controllers;

use Cadexsa\Presentation\View;
use Cadexsa\Domain\Model\Persistence;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ExStudentsController extends Controller
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $exStudents = Persistence::exStudentRepository()->all();
        $exStudentCount = Persistence::exStudentRepository()->count();
        $view_params = ['exStudents' => $exStudents, 'exStudentCount' => $exStudentCount];

        return $this->prepareResponseFromView(new View(views_path("exstudents.php"), $view_params));
    }
}

<?php

declare(strict_types=1);

namespace Cadexsa\Presentation\Http\Controllers;

use Cadexsa\Presentation\View;
use Cadexsa\Services\Registry;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AccountActivationController extends Controller
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $exStudentId = (int) $request->getQueryParams()['id'];

        try {
            Registry::exStudentService()->activateExStudent($exStudentId);
            $view_params['response'] = "Your account has been activated.";
        } catch (\Throwable $e) {
            $view_params['response'] = $e->getMessage();
        }

        $_SESSION['token'] = $view_params['token'] = csrf_token();

        return $this->prepareResponseFromView(new View(views_path("login.php"), $view_params));
    }
}

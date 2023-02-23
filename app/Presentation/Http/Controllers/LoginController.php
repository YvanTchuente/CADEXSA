<?php

declare(strict_types=1);

namespace Cadexsa\Presentation\Http\Controllers;

use Cadexsa\Presentation\View;
use Cadexsa\Services\Registry;
use Cadexsa\Domain\ServiceRegistry;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class LoginController extends Controller
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $method = strtoupper($request->getMethod());

        switch ($method) {
            case "GET":
                if (ServiceRegistry::authenticationService()->check()) {
                    Registry::exStudentService()->logout();
                    $view_params['response'] = "You have logged out";
                } else {
                    if (isset($_SESSION['goto'])) {
                        $goto = $_SESSION['goto'];
                        unset($_SESSION['goto']);
                        $view_params['goto'] = $goto;
                    }
                    if ($request->getQueryParams()) {
                        if (isset($request->getQueryParams()['goto'])) {
                            $goto = urldecode($request->getQueryParams()['goto']);
                            $view_params['goto'] = $goto;
                            $_SESSION['goto'] = $goto;
                        }
                    }
                }
                break;

            case "POST":
                $username = $request->getParsedBody()['username'];
                $password = $request->getParsedBody()['password'];

                try {
                    $exstudent = Registry::exStudentService()->login($username, $password);

                    $username = strtolower($exstudent->getUsername());
                    if (isset($request->getParsedBody()['goto'])) {
                        if (isset($_SESSION['goto']) && $request->getParsedBody()['goto'] == $_SESSION['goto']) {
                            unset($_SESSION['goto']);
                        }
                        header('Location: ' . $request->getParsedBody()['goto']);
                    } else {
                        header("Location: /exstudents/$username");
                    }

                    exit();
                } catch (\RuntimeException $e) {
                    $view_params['response'] = $e->getMessage();
                }
                break;
        }

        $_SESSION['token'] = $view_params['token'] = csrf_token();

        return $this->prepareResponseFromView(new View(views_path("login.php"), $view_params));
    }
}

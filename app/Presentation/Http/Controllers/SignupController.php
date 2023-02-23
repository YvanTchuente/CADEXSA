<?php

declare(strict_types=1);

namespace Cadexsa\Presentation\Http\Controllers;

use Cadexsa\Presentation\View;
use Cadexsa\Services\Registry;
use Cadexsa\Domain\ServiceRegistry;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Cadexsa\Domain\Model\ExStudent\Orientation;

class SignupController extends Controller
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $method = strtoupper($request->getMethod());

        if ($method == "POST") {
            if (ServiceRegistry::authenticationService()->check()) {
                header("Location: /exstudents/login");
            }

            $payload = $request->getParsedBody();
            $username = $payload['username'];
            $password = $payload['password'];
            $confirmationPassword = $payload['confirmation_password'];
            $firstname = $payload['firstname'];
            $lastname = $payload['lastname'];
            $email = $payload['email'];
            $city = $payload['city'];
            $country = $payload['country'];
            $mainPhoneNumber = $payload['phone_number'];
            $batchYear = $payload['batch_year'];
            $description = $payload['description'];
            $orientation = Orientation::from($payload['orientation']);

            try {
                if ($password !== $confirmationPassword) {
                    throw new \RuntimeException("The passwords mismatch.");
                }

                Registry::exStudentService()->registerExStudent($username, $password, $firstname, $lastname, $email, $city, $country, $mainPhoneNumber, $batchYear, $description, $orientation);

                $view_params['response'] = "A link to activate your account has been emailed to the address provided.";
            } catch (\RuntimeException $e) {
                $view_params['response'] = $e->getMessage();
            }
        }

        $_SESSION['token'] = $view_params['token'] = csrf_token();

        return $this->prepareResponseFromView(new View(views_path("signup.php"), $view_params));
    }
}

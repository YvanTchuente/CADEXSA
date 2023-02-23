<?php

declare(strict_types=1);

namespace Cadexsa\Presentation\Http\Controllers;

use Cadexsa\Presentation\View;
use Cadexsa\Domain\Model\INull;
use Cadexsa\Domain\Model\Persistence;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Cadexsa\Infrastructure\Persistence\Criteria;

class PasswordResetController extends Controller
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $method = strtoupper($request->getMethod());

        switch ($method) {
            case "GET":
                $view_params['step'] = 1;
                $requestParams = $request->getQueryParams();
                if (isset($requestParams['key'])) {
                    try {
                        $connection = app()->database->getConnection();
                        $connection->pdo->beginTransaction();
                        extract($requestParams);
                        $stmt = $connection->pdo->prepare("SELECT * FROM password_reset_attempts WHERE reset_key = ?");
                        $stmt->execute([$key]);
                        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

                        if (!$row) {
                            $view_params['step'] = 1;
                            throw new \RuntimeException("Invalid reset key");
                        }

                        extract($row);
                        $submittedAt = strtotime($timestamp);
                        $duration_of_validity = 3 * 24 * 60 * 60; // 3 days
                        $expiration_timestamp = $submittedAt + $duration_of_validity;
                        $current_timestamp = time();
                        $timeElapsed = $expiration_timestamp - $current_timestamp; // Time interval since reset email was sent

                        if ($timeElapsed <= $duration_of_validity) {
                            $view_params['exStudentId'] = $exStudentId;
                            $view_params['step'] = 3;
                        } else {
                            $view_params['step'] = 1;
                            throw new \RuntimeException("The reset key has expired");
                            $stmt = $connection->pdo->prepare("DELETE FROM password_reset_attempts WHERE exStudentId = ?");
                            $stmt->execute([$exStudentId]);
                        }
                        $connection->pdo->commit();
                    } catch (\Exception $e) {
                        $connection->pdo->rollBack();
                        switch (true) {
                            case ($e instanceof \PDOException):
                                $view_params['message'] = "We have encountered an error";
                                break;
                            default:
                                $view_params['message'] = $e->getMessage();
                                break;
                        }
                    }
                }
                break;

            case "POST":
                $requestParams = $request->getParsedBody();

                try {
                    $connection = app()->database->getConnection();
                    $connection->pdo->beginTransaction();

                    switch (true) {
                        case (isset($requestParams['username'])):
                            $username = $requestParams['username'];
                            $exstudent = Persistence::exStudentRepository()->selectMatch(Criteria::equal('username', $username));

                            if ($exstudent instanceof INull) {
                                throw new \RuntimeException("Invalid username");
                            } else {
                                $view_params['exStudentId'] = $exstudent->getId();
                                $view_params['step'] = 2;
                            }
                            break;

                        case (isset($requestParams['email']) && isset($requestParams['exStudentId'])):
                            extract($requestParams);
                            $exStudentId = (int) $exStudentId;
                            $key = hash('sha512', random_bytes(32));
                            $link = (string) $request->getUri()->withQuery("key=$key");

                            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                throw new \RuntimeException("Invalid email address");
                            }

                            // Verify that the ex-student has not already attempted
                            $stmt = $connection->pdo->prepare("SELECT * FROM password_reset_attempts WHERE exStudentId = ?");
                            $stmt->execute([$exStudentId]);

                            if ($stmt->fetch(\PDO::FETCH_ASSOC)) {
                                $view_params['step'] = 2;
                                throw new \RuntimeException("You have already attempted to reset your password, Check you mails for the key.");
                            }

                            $subject = "Reset your password";
                            $username = Persistence::exStudentRepository()->findById($exStudentId)->getUsername();
                            $password_reset_email_view = new View("emails/password_reset_email", ['host' => $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'], 'username' => $username, 'link' => $link]);
                            $mailer = app()->getMailer();
                            $mailer->from(config("mail.accounts.members"), 'Cadexsa Accounts');
                            $mailer->to($email);

                            try {
                                $mailer->send($password_reset_email_view->render(), $subject);
                            } catch (\Exception $e) {
                                // throw new \RuntimeException("We have encountered an error");
                            }

                            $stmt = $connection->pdo->prepare("INSERT INTO password_reset_attempts (exStudentId, email, reset_key) VALUES (?,?,?)");
                            $stmt->execute([$exStudentId, $email, $key]);
                            $view_params['message'] = "If that email address is in our database, we will send you an email to reset your password.";
                            $view_params['step'] = 2;
                            break;

                        case (isset($requestParams['new_password']) && isset($requestParams['exStudentId'])):
                            extract($requestParams);

                            if (!(ctype_alnum($new_password) && strlen($new_password) >= 10)) {
                                $view_params['exStudentId'] = $exStudentId;
                                throw new \RuntimeException("Invalid password");
                            }

                            $hashed_password = password_hash($new_password, PASSWORD_ARGON2ID);
                            $stmt = $connection->pdo->prepare("UPDATE members SET password = ? WHERE id = ?");
                            $stmt->execute([$hashed_password, $exStudentId]);
                            $stmt = $connection->pdo->prepare("DELETE FROM password_reset_attempts WHERE exStudentId = ?");
                            $stmt->execute([$exStudentId]);
                            $connection->pdo->commit();
                            header('Location: /exstudents/login');
                            exit();
                            break;
                    }
                    $connection->pdo->commit();
                } catch (\Throwable $e) {
                    if ($connection) {
                        $connection->pdo->rollBack();
                    }
                    $view_params['step'] = (int) $requestParams['current-step'];

                    if ($e instanceof \PDOException) {
                        $view_params['message'] = "A fatal error occurred.";
                    } else {
                        $view_params['message'] = $e->getMessage();
                    }
                }
                break;
        }

        $_SESSION['token'] = $view_params['token'] = csrf_token();
        $password_reset_view = new View(views_path("password_reset.php"), $view_params);

        return $this->prepareResponseFromView($password_reset_view);
    }
}

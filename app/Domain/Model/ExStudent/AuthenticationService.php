<?php

declare(strict_types=1);

namespace Cadexsa\Domain\Model\ExStudent;

use Cadexsa\Domain\Model\INull;
use Cadexsa\Domain\Model\Persistence;
use Cadexsa\Domain\Model\EventDispatcher;
use Cadexsa\Infrastructure\Persistence\Criteria;
use Cadexsa\Domain\Exceptions\AuthenticationException;

class AuthenticationService
{
    /**
     * Logs a user into the application.
     * 
     * @param string $username The username of the ex-student
     * @param string $password The password of the ex-student
     * 
     * @return ExStudent The ex-student.
     * 
     * @throws AuthenticationException If an error occurs.
     */
    public function login(string $username, string $password)
    {
        try {
            // Authenticate the user
            $exstudent = Persistence::exStudentRepository()->selectMatch(Criteria::equal('username', $username));

            if (($exstudent instanceof INull) or (!password_verify($password, $exstudent->getPassword())) or ($exstudent->getStatus() === Status::SUSPENDED)) {
                throw new AuthenticationException("Invalid username or password.", route('login'));
            }
            if ($this->check()) {
                throw new AuthenticationException("Someone is currently logged-in with this account.", route('login'));
            }
            if ($exstudent->getStatus() !== Status::ACTIVE) {
                throw new AuthenticationException("This account needs activation.", route('login'));
            }

            /**
             * Log in the ex-student
             * Registers the ex-student to the online_members table
             */
            $uid = md5($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);
            $stmt = app()->database->getConnection()->pdo->prepare("INSERT INTO online_members (exStudentId, uid) VALUES (?, ?)");
            $inserted = $stmt->execute([$exstudent->getId(), $uid]);

            // Registers the ex-student onto the authenticated session
            session_regenerate_id();
            $_SESSION['exstudent'] = $exstudent;

            EventDispatcher::getInstance()->dispatch(new ExStudentLoggedIn($exstudent->getUsername())); // Dispatch the event

            return $exstudent;
        } catch (\PDOException $e) {
            throw new \RuntimeException("Error occurred during login");
        }
    }

    /**
     * Determines if the current user or an a given ex-sudent is authenticated.
     * 
     * @param string $username [optional] The username of an ex-student.
     *
     * @return bool
     */
    public function check(string $username = null)
    {
        if ($username) {
            $exstudent = Persistence::exStudentRepository()->selectMatch(Criteria::equal('username', $username));

            if ($exstudent instanceof INull) {
                throw new AuthenticationException("$username is not a registered ex-student");
            }

            $stmt = app()->database->getConnection()->pdo->prepare("SELECT * FROM online_members WHERE exStudentId = ?");
            $stmt->execute([$exstudent->getId()]);

            return boolval($stmt->rowCount());
        } else {
            if (isset($_SESSION['exstudent'])) {
                $exstudent = $_SESSION['exstudent'];

                $stmt = app()->database->getConnection()->pdo->prepare("SELECT * FROM online_members WHERE exStudentId = ?");
                $stmt->execute([$exstudent->getId()]);

                if ($stmt->rowCount() === 1) {
                    return true;
                } else {
                    unset($_SESSION['exstudent']);
                }
            } else {
                $uid = md5($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);
                $stmt = app()->database->getConnection()->pdo->prepare("SELECT * FROM online_members WHERE uid = ?");
                $stmt->execute([$uid]);

                if ($stmt->rowCount() === 1) {
                    app()->database->getConnection()->pdo->exec("DELETE FROM online_members WHERE uid = '$uid'");
                }
            }

            return false;
        }
    }

    /**
     * Determine if the current user is a guest or a given ex-student is offline.
     * 
     * @param string $username [optional] The username of an ex-student.
     *
     * @return bool
     */
    public function guest(string $username = null)
    {
        return !$this->check($username);
    }

    /**
     * Log the user out of the application.
     */
    public function logout()
    {
        try {
            if ($this->check()) {
                $exstudent = $_SESSION['exstudent'];
                $exstudentId = $exstudent->getId();

                // Unregisters the ex-student from the online_member table
                $stmt = app()->database->getConnection()->pdo->prepare("DELETE FROM online_members WHERE exStudentId = ?");
                $stmt->execute([$exstudent->getId()]);

                // Updates the ex-student's last session timestamp
                $stmt = app()->database->getConnection()->pdo->prepare("UPDATE exstudents SET last_session_on = CURRENT_TIMESTAMP() WHERE id = ?");
                $stmt->execute([$exstudent->getId()]);

                // Unset auhenticated session data
                unset($_SESSION['exstudent'], $exstudent);

                EventDispatcher::getInstance()->dispatch(new ExStudentLoggedOut($exstudentId)); // Dispatch the event
            }
        } catch (\Throwable $e) {
            // Do nothing
        }
    }
}

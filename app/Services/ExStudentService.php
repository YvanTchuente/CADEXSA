<?php

declare(strict_types=1);

namespace Cadexsa\Services;

use Cadexsa\Domain\Model\INull;
use Cadexsa\Domain\ServiceRegistry;
use Cadexsa\Domain\Model\Persistence;
use Cadexsa\Domain\Model\EventDispatcher;
use Cadexsa\Domain\Model\ExStudent\Status;
use Cadexsa\Domain\Model\ListenerProvider;
use Cadexsa\Domain\Model\ExStudent\ExStudent;
use Cadexsa\Domain\Model\ExStudentSubscriber;
use Cadexsa\Domain\Model\ExStudent\Orientation;
use Cadexsa\Domain\Exceptions\AuthenticationException;
use Cadexsa\Infrastructure\Transaction\TransactionManager;

class ExStudentService
{
    /**
     * Registers an ex-student for membership.
     * 
     * @return ExStudent The ex-student.
     *
     * @throws \RuntimeException if an error occurs.
     */
    public function registerExStudent(
        string $username,
        string $password,
        string $firstname,
        string $lastname,
        string $email,
        string $city,
        string $country,
        string $phoneNumber,
        string|int $batchYear,
        string $description,
        Orientation $orientation,
    ): ExStudent {
        try {
            $provider = new ListenerProvider;
            $subscriber = new ExStudentSubscriber;
            $provider->addListener([$subscriber, "onExstudentRegistered"]);
            EventDispatcher::begin($provider);
            $exstudent = ServiceRegistry::memberRegistrationService()->registerExStudent($username, $password, $firstname, $lastname, $email, $city, $country, $phoneNumber, $batchYear, $description, $orientation);

            return $exstudent;
        } finally {
            EventDispatcher::end();
        }
    }

    /**
     * Activates an ex-student.
     *
     * @param int $exStudentId The ex-student's identifier.
     * 
     * @throws \RuntimeException The ex-student is inexistent.
     */
    public function activateExStudent(int $exStudentId)
    {
        $exstudent = Persistence::exStudentRepository()->findById($exStudentId);

        if ($exstudent instanceof INull) {
            throw new AuthenticationException("This account does not exist.");
        }

        $exstudent->setStatus(Status::ACTIVE);
        TransactionManager::dirty($exstudent);
    }

    /**
     * Suspends a ex-student.
     *
     * @param int $exStudentId The ex-student's identifier.
     * 
     * @throws \RuntimeException The ex-student is inexistent.
     */
    public function suspendExStudent(int $exStudentId)
    {
        $exstudent = Persistence::exStudentRepository()->findById($exStudentId);

        if ($exstudent instanceof INull) {
            throw new \RuntimeException("This account does not exist.");
        }

        $exstudent->setStatus(Status::SUSPENDED);
        TransactionManager::dirty($exstudent);
    }

    /**
     * Logs a ex-student into the application.
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
            $provider = new ListenerProvider;
            $subscriber = new ExStudentSubscriber;
            $provider->addListener([$subscriber, "onLogin"]);
            EventDispatcher::begin($provider);
            $exstudent = ServiceRegistry::authenticationService()->login($username, $password);
            
            return $exstudent;
        } catch (\Exception $e) {
            app()->getLogger()->info("A login attempt failed from a device at {address}", ['address' => $_SERVER['REMOTE_ADDR']]);
            throw $e;
        } finally {
            EventDispatcher::end();
        }
    }

    /**
     * Logs out the currently logged-in ex-student.
     */
    public function logout()
    {
        try {
            $provider = new ListenerProvider;
            $subscriber = new ExStudentSubscriber;
            $provider->addListener([$subscriber, "onLogout"]);
            EventDispatcher::begin($provider);
            ServiceRegistry::authenticationService()->logout();
        } finally {
            EventDispatcher::end();
        }
    }
}

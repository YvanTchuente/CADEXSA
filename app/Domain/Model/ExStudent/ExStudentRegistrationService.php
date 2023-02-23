<?php

declare(strict_types=1);

namespace Cadexsa\Domain\Model\ExStudent;

use Cadexsa\Domain\Model\Persistence;
use Cadexsa\Domain\Model\EventDispatcher;
use Cadexsa\Domain\Factories\ExStudentFactory;

class ExStudentRegistrationService
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
        Orientation $orientation
    ): ExStudent {
        $exstudent = ExStudentFactory::create($username, $password, $firstname, $lastname, $city, $country, $email, $phoneNumber, $batchYear, $orientation, $description);
        Persistence::exStudentRepository()->add($exstudent);
        EventDispatcher::getInstance()->dispatch(new ExstudentRegistered($exstudent)); // Dispatch event

        return $exstudent;
    }
}

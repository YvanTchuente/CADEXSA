<?php

declare(strict_types=1);

namespace Cadexsa\Domain\Factories;

use Cadexsa\Domain\Model\ExStudent\Name;
use Cadexsa\Domain\Model\ExStudent\Level;
use Cadexsa\Domain\Model\ExStudent\Status;
use Cadexsa\Domain\Model\ExStudent\Address;
use Cadexsa\Domain\Model\ExStudent\ExStudent;
use Cadexsa\Domain\Model\ExStudent\Orientation;

class ExStudentFactory extends EntityFactory
{
    /**
     * Provisions an ex-student.
     */
    public static function create(
        string $username,
        string $password,
        string $firstname,
        string $lastname,
        string $city,
        string $country,
        string $email,
        string $phoneNumber,
        string $batchYear,
        Orientation $orientation,
        string $description,
        Level $level = Level::REGULAR,
        Status $status = Status::UNACTIVATED,
        string $avatar = null,
    ): ExStudent {
        $id = app()->IdGenerator()->generateId();
        $name = new Name($firstname, $lastname);
        $address = new Address($country, $city);
        $exstudent = new ExStudent($id, $username, $password, $name, $address, $email, $phoneNumber, $batchYear, $orientation, $description, $level, $status, $avatar);

        return $exstudent;
    }

    /**
     * Reconstitutes a ex-student from its stored representation.
     * 
     * @param array $resultSet An associative array of record data.
     */
    public function reconstitute(array $resultSet): ExStudent
    {
        $this->validateResults($resultSet);
        extract($resultSet);
        $name = new Name($firstname, $lastname);
        $address = new Address($country, $city);
        $level = Level::from((int) $level);
        $orientation = Orientation::from($orientation);
        $status = Status::from((int) $status);

        // Set the ex-student's properties
        $exstudent = new ExStudent($id, $username, $password, $name, $address, $email_address, $phone_number, $batch_year, $orientation, $description, $level, $status, $avatar, $registered_on);
        if ($last_session_on) {
            $exstudent->setLastSessionDate($last_session_on);
        }

        // Set the ex-student's password in its hashed form via reflection
        $password_property = new \ReflectionProperty(ExStudent::class, 'password');
        $password_property->setValue($exstudent, $password);

        return $exstudent;
    }
}

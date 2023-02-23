<?php

declare(strict_types=1);

namespace Cadexsa\Domain\Model\ExStudent;

use Cadexsa\Domain\Model\Entity;
use Cadexsa\Domain\ServiceRegistry;

/**
 * Represents a registered ex-student.
 */
class ExStudent extends Entity
{
    /**
     * The ex-student's username.
     */
    private string $username;

    /**
     * The ex-student's password.
     */
    private string $password;

    /**
     * The ex-student's name.
     */
    private Name $name;

    /**
     * The ex-student's address.
     */
    private Address $address;

    /**
     * The ex-student's email address.
     */
    private string $email;

    /**
     * The ex-student's telephone number.
     */
    private string $phoneNumber;

    /**
     * The ex-student's batch year.
     */
    private string $batchYear;

    /**
     * The ex-student's orientation.
     */
    private Orientation $orientation;

    /**
     * The ex-student's description.
     */
    private string $description;

    /**
     * The ex-student's level.
     */
    private Level $level;

    /**
     * The ex-student's status.
     */
    private Status $status;

    /**
     * The URI of the ex-student's avatar.
     */
    private string $avatar;

    /**
     * The ex-student's registration date.
     */
    private string $registeredOn;

    /**
     * The timestamp of the ex-student's last session.
     *
     * @var string|null
     */
    private ?string $lastSessionOn = null;

    public function __construct(
        int $id,
        string $username,
        string $password,
        Name $name,
        Address $address,
        string $email,
        string $phoneNumber,
        string $batchYear,
        Orientation $orientation,
        string $description,
        Level $level = Level::REGULAR,
        Status $status = Status::UNACTIVATED,
        string $avatar = null,
        string $registrationDate = null
    ) {
        parent::__construct($id);
        $this->setUsername($username);
        $this->setPassword($password);
        $this->setName($name);
        $this->setAddress($address);
        $this->setEmailAddress($email);
        $this->setPhoneNumber($phoneNumber);
        $this->setOrientation($orientation);
        $this->setBatchYear($batchYear);
        $this->setDescription($description);
        $this->setLevel($level);
        $this->setStatus($status);
        $this->setAvatar($avatar);
        $this->setRegistrationDate($registrationDate ?? date('Y-m-d H:i:s'));
    }

    /**
     * Sets the ex-student's username.
     */
    public function setUsername(string $username)
    {
        if (!$username) {
            throw new \LengthException("The ex-student's username is required.");
        }
        $this->username = $username;

        return $this;
    }

    /**
     * Sets the ex-student's password.
     */
    public function setPassword(string $password)
    {
        if (strlen($password) <= 9) {
            throw new \LengthException("The password must at least be 10 characters long.");
        }
        $this->password = password_hash($password, PASSWORD_ARGON2ID);

        return $this;
    }

    /**
     * Sets the ex-student's email address.
     */
    public function setEmailAddress(string $address)
    {
        if (!filter_var($address, FILTER_VALIDATE_EMAIL)) {
            throw new \DomainException("Invalid email address.");
        }
        $this->email = $address;

        return $this;
    }

    /**
     * Sets the ex-student's name.
     */
    public function setName(Name $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Sets the ex-student's telephone number.
     */
    public function setPhoneNumber(string $phoneNumber)
    {
        if (!preg_match('/^(\(+\d{3}\s\))?\d{9}$/', $phoneNumber)) {
            throw new \DomainException("Invalid telephone number.");
        }
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * Sets the ex-student's address.
     */
    public function setAddress(Address $address)
    {
        $this->address = $address;
    }

    /**
     * Sets the ex-student's graduation year.
     */
    public function setBatchYear(string $batchYear)
    {
        if (!preg_match('/^\d{4}$/', $batchYear)) {
            throw new \DomainException("Invalid year.");
        }
        $this->batchYear = (string) $batchYear;

        return $this;
    }

    /**
     * Sets the ex-student's field orientation.
     */
    public function setOrientation(Orientation $orientation)
    {
        $this->orientation = $orientation;

        return $this;
    }

    /**
     * Sets the ex-student's description.
     */
    public function setDescription(string $description)
    {
        if (!$description) {
            throw new \LengthException("The ex-student's description is required.");
        }
        $this->description =  $description;

        return $this;
    }

    /**
     * Sets the ex-student's priviledge level.
     */
    public function setLevel(Level $level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Sets the ex-student's status.
     */
    public function setStatus(Status $status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Sets the timestamp at which the ex-student terminated their last session.
     */
    public function setLastSessionDate(string $timestamp)
    {
        $this->lastSessionOn = $this->validateTimestamp($timestamp);

        return $this;
    }

    /**
     * Sets the ex-student's registration timestamp.
     */
    public function setRegistrationDate(string $timestamp)
    {
        if (isset($this->registeredOn)) {
            throw new \LogicException('The registration date cannot be changed once set.');
        }
        $this->registeredOn = $this->validateTimestamp($timestamp);

        return $this;
    }

    /**
     * Sets the ex-student's profile picture.
     *
     * @param string|null $avatar The URI of the avatar file.
     */
    public function setAvatar(string $avatar = null)
    {
        if (is_null($avatar)) {
            $avatar = config("accounts.avatars.default");
        } else if (!$avatar) {
            throw new \LengthException("Invalid URI: empty string passed.");
        }
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Retrieves the ex-student's hashed password.
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Retrieves the ex-student's username.
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Retrieves the ex-student's email address.
     */
    public function getEmailAddress()
    {
        return $this->email;
    }

    /**
     * Retrieves the ex-student's name.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Retrieves the ex-student's phone number.
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * Retrieves the ex-student's address.
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Retrieves the ex-student's batch year.
     */
    public function getBatchYear()
    {
        return $this->batchYear;
    }

    /**
     * Retrieves the ex-student's field orientation.
     */
    public function getOrientation()
    {
        return $this->orientation;
    }

    /**
     * Sets the ex-student's description.
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Retrieves the ex-student's priviledge level.
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Retrieves the ex-student's profile picture.
     * 
     * @return string The URI of the picture.
     */
    public function getAvatar()
    {
        $doc_root = str_replace("/", "\\", $_SERVER['DOCUMENT_ROOT']);
        $avatar = str_replace($doc_root, "", $this->avatar);
        return $avatar;
    }

    /**
     * Retrieves the ex-student's account status.
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Returns the ex-student's last session termination timestamp.
     * 
     * @var \Datetime|null
     */
    public function getLastSessionDate()
    {
        if (isset($this->lastSessionOn)) {
            return new \DateTime($this->lastSessionOn);
        } else {
            return null;
        }
    }

    /**
     * Returns the ex-student's registration timestamp.
     */
    public function getRegistrationDate()
    {
        return new \DateTime($this->registeredOn);
    }

    /**
     * Determines the ex-student's current state.
     */
    public function state(): State
    {
        $isLoggedIn = ServiceRegistry::authenticationService()->check($this->username);

        return State::from((int) $isLoggedIn);
    }
}

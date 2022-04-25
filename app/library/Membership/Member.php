<?php

declare(strict_types=1);

namespace Application\Membership;

/**
 * Describes a registered member
 */
class Member
{
    /**
     * @var string
     */
    protected $ID;

    /**
     * @var string
     */
    protected $firstname;

    /**
     * @var string
     */
    protected $lastname;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $contact;

    /**
     * @var string
     */
    protected $country;

    /**
     * @var string
     */
    protected $city;

    /**
     * @var string
     */
    protected $batchYear;

    /**
     * @var MemberOrientation
     */
    protected $orientation;

    /**
     * @var string
     */
    protected $aboutme;

    /**
     * @var MemberLevel
     */
    protected $level;

    /**
     * @var string
     */
    protected $picture;

    /**
     * @var string
     */
    protected $lastConnection;

    /**
     * @var string
     */
    protected $registrationDate;

    public function setID(int $ID)
    {
        if ($ID == 0) {
            throw new \InvalidArgumentException("Invalid ID");
        }
        $this->ID = $ID;
        return $this;
    }

    public function setFirstName(string $firstname)
    {
        if (empty($firstname)) {
            throw new \InvalidArgumentException("Invalid argument");
        }
        $this->firstname = $firstname;
        return $this;
    }

    public function setLastName(string $lastname)
    {
        if (empty($lastname)) {
            throw new \InvalidArgumentException("Invalid argument");
        }
        $this->lastname = $lastname;
        return $this;
    }

    public function setUserName(string $username)
    {
        if (empty($username)) {
            throw new \InvalidArgumentException("Invalid argument");
        }
        $this->username = $username;
        return $this;
    }

    public function setEmail(string $email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Invalid email");
        }
        $this->email = $email;
        return $this;
    }

    public function setContact(string $contact)
    {
        if (empty($contact)) {
            throw new \InvalidArgumentException("Invalid argument");
        }
        $this->contact = $contact;
        return $this;
    }

    public function setCountry(string $country)
    {
        if (empty($country)) {
            throw new \InvalidArgumentException("Invalid argument");
        }
        $this->country = $country;
        return $this;
    }

    public function setCity(string $city)
    {
        if (empty($city)) {
            throw new \InvalidArgumentException("Invalid argument");
        }
        $this->city = $city;
        return $this;
    }

    public function setBatch(string $batchYear)
    {
        if (!preg_match('/\d{4}/', $batchYear)) {
            throw new \InvalidArgumentException("Invalid argument");
        }
        $this->batchYear = $batchYear;
        return $this;
    }

    public function setOrientation(MemberOrientation $orientation)
    {
        $this->orientation = $orientation;
        return $this;
    }

    public function setAboutMe(string $aboutme)
    {
        if (empty($aboutme)) {
            throw new \InvalidArgumentException("Invalid argument");
        }
        $this->aboutme =  $aboutme;
        return $this;
    }

    public function setLevel(MemberLevel $level)
    {
        $this->level = $level;
        return $this;
    }

    public function setLastConnection(string $lastConnection)
    {
        if (empty($lastConnection) || !strtotime($lastConnection)) {
            throw new \InvalidArgumentException("Invalid date");
        }
        $this->lastConnection = $lastConnection;
        return $this;
    }

    public function setRegistrationDate(string $registrationDate)
    {
        if (empty($registrationDate) || !strtotime($registrationDate)) {
            throw new \InvalidArgumentException("Invalid date");
        }
        $this->registrationDate = $registrationDate;
        return $this;
    }

    public function setPicture(string $pictureLocation)
    {
        if (empty($pictureLocation)) {
            throw new \InvalidArgumentException("Invalid argument");
        }
        $this->picture = $pictureLocation;
        return $this;
    }

    public function getID()
    {
        return $this->ID;
    }

    public function getFirstName()
    {
        return $this->firstname;
    }

    public function getLastName()
    {
        return $this->lastname;
    }

    public function getName()
    {
        $name = implode(" ", [$this->getLastName(), $this->getFirstName()]);
        return $name;
    }

    public function getUserName()
    {
        return $this->username;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getContact()
    {
        return $this->contact;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function getBatch()
    {
        return $this->batchYear;
    }

    public function getOrientation()
    {
        $orientation = $this->orientation->value;
        return $orientation;
    }

    public function getAboutme()
    {
        return $this->aboutme;
    }

    public function getLevel()
    {
        $level = ucfirst(strtolower($this->level->name));
        return $level;
    }

    public function getLastConnection()
    {
        if (empty($this->lastConnection)) {
            return "";
        }
        return $this->lastConnection;
    }

    public function getRegistrationDate()
    {
        return $this->registrationDate;
    }

    public function getPicture()
    {
        return $this->picture;
    }
}

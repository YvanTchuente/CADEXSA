<?php

declare(strict_types=1);

namespace Application\Membership;

class MemberBuilder implements MemberBuilderInterface
{
    private $member;

    public function __construct()
    {
        $this->member = new Member();
    }

    public function setMemberID(int $id)
    {
        $this->member->setID($id);
        return $this;
    }

    public function setMemberFirstName(string $firstname)
    {
        $this->member->setFirstName($firstname);
        return $this;
    }

    public function setMemberLastName(string $lastname)
    {
        $this->member->setLastName($lastname);
        return $this;
    }

    public function setMemberUserName(string $username)
    {
        $this->member->setUserName($username);
        return $this;
    }

    public function setMemberEmail(string $email)
    {
        $this->member->setEmail($email);
        return $this;
    }

    public function setMemberContact(string $contact)
    {
        $this->member->setContact($contact);
        return $this;
    }

    public function setMemberCountry(string $country)
    {
        $this->member->setCountry($country);
        return $this;
    }

    public function setMemberCity(string $city)
    {
        $this->member->setCity($city);
        return $this;
    }

    public function setMemberBatch(string $batchYear)
    {
        $this->member->setBatch($batchYear);
        return $this;
    }

    public function setMemberOrientation(MemberOrientation|string $orientation)
    {
        if (is_string($orientation)) {
            $orientation = MemberOrientation::from($orientation);
        }
        $this->member->setOrientation($orientation);
        return $this;
    }

    public function setMemberAboutMe(string $aboutme)
    {
        $this->member->setAboutMe($aboutme);
        return $this;
    }

    public function setMemberLevel(MemberLevel|int $level)
    {
        if (is_integer($level)) {
            $level = MemberLevel::from($level);
        }
        $this->member->setLevel($level);
        return $this;
    }

    public function setMemberLastConnection(string $lastConnection)
    {
        $this->member->setLastConnection($lastConnection);
        return $this;
    }
    public function setMemberRegistrationDate(string $registrationDate)
    {
        $this->member->setRegistrationDate($registrationDate);
        return $this;
    }

    public function setMemberPicture(string $pictureLocation)
    {
        $this->member->setPicture($pictureLocation);
        return $this;
    }

    public function getMember()
    {
        $member = $this->member;
        $this->member = new Member();
        return $member;
    }
}

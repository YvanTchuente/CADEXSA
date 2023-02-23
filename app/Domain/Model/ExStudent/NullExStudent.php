<?php

declare(strict_types=1);

namespace Cadexsa\Domain\Model\ExStudent;

use Cadexsa\Domain\Model\INull;
use Cadexsa\Domain\Model\NullEntityTrait;

abstract class NullExStudent extends ExStudent implements INull
{
    use NullEntityTrait;

    public function setUsername(string $username)
    {
    }

    public function setPassword(string $password)
    {
    }

    public function setEmailAddress(string $email)
    {
    }

    public function setName(Name $name)
    {
    }

    public function setPhoneNumber(string $phoneNumber)
    {
    }

    public function setAddress(Address $address)
    {
    }

    public function setBatchYear(string|int $batchYear)
    {
    }

    public function setOrientation(Orientation $orientation)
    {
    }

    public function setDescription(string $description)
    {
    }

    public function setLevel(Level $level)
    {
    }

    public function setStatus(Status $status)
    {
    }

    public function setLastSessionDate(string $timestamp)
    {
    }

    public function setRegistrationDate(string $timestamp)
    {
    }

    public function setAvatar(string $avatar = null)
    {
    }

    public function getUsername()
    {
        return "";
    }

    public function getEmailAddress()
    {
        return "";
    }

    public function getName()
    {
        return new NullName;
    }

    public function getPhoneNumber()
    {
        return "";
    }

    public function getAddress()
    {
        return new NullAddress;
    }

    public function getBatchYear()
    {
        return (int) date('Y');
    }

    public function getOrientation()
    {
        return Orientation::ARTS;
    }

    public function getDescription()
    {
        return "";
    }

    public function getLevel()
    {
        return Level::VISITOR;
    }

    public function getAvatar()
    {
        return DEFAULT_PROFILE_PICTURE;
    }

    public function getStatus()
    {
        return Status::SUSPENDED;
    }

    public function state(): State
    {
        return State::OFFLINE;
    }

    public function getLastSessionDate()
    {
        return date('Y-m-d H:i:s');
    }

    public function getRegistrationDate()
    {
        return date('Y-m-d H:i:s');
    }
}

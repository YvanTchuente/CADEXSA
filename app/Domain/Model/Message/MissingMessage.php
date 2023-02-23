<?php

declare(strict_types=1);

namespace Cadexsa\Domain\Model\Message;

use Cadexsa\Domain\Model\INull;
use Cadexsa\Domain\Model\NullEntityTrait;
use Cadexsa\Domain\Model\ExStudent\MissingExStudent;

class MissingMessage extends Message implements INull
{
    use NullEntityTrait;

    public function from(int $senderId)
    {
        return $this;
    }

    public function setReceiver(int $receiverId)
    {
        return $this;
    }

    public function setBody(string $message)
    {
        return $this;
    }

    public function setCreatedAt(string $timestamp)
    {
        return $this;
    }

    public function setStatus(Status $status)
    {
        return $this;
    }

    public function getSender()
    {
        return new MissingExStudent;
    }

    public function getReceiver()
    {
        return new MissingExStudent;
    }

    public function getBody()
    {
        return "";
    }

    public function createdAt()
    {
        return date('r');
    }

    public function getStatus()
    {
        return Status::UNREAD;
    }

    public function isRead()
    {
        return false;
    }
}

<?php

declare(strict_types=1);

namespace Cadexsa\Domain\Model\Message;

use Cadexsa\Domain\Model\Entity;
use Cadexsa\Domain\Model\Persistence;

/**
 * Represents a chat message.
 */
class Message extends Entity
{
    /**
     * The identifier of the message's sender.
     */
    private int $senderId;

    /**
     * The identifier of the message's receiver.
     */
    private int $receiverId;

    /**
     * The message's body.
     */
    private string $body;

    /**
     * The message's status.
     */
    private Status $status;

    /**
     * The date and time at which the message was created.
     */
    private string $createdAt;

    public function __construct(int $id, int $senderId, int $receiverId, string $body, Status $status = Status::UNREAD, string $creationDate = null)
    {
        parent::__construct($id);
        $this->setFrom($senderId);
        $this->setReceiver($receiverId);
        $this->setBody($body);
        $this->setCreatedAt($creationDate ?? date("Y-m-d H:i:s"));
        $this->setStatus($status);
    }

    /**
     * Sets the sender of the message.
     *
     * @param int $senderId The sender's identifier.
     */
    public function setFrom(int $senderId)
    {
        if (isset($this->senderId)) {
            throw new \LogicException("The message's sender must not be changed once set.");
        }
        if (!$senderId) {
            throw new \DomainException("The identifier must be a non-zero number.");
        }
        $this->senderId = $senderId;

        return $this;
    }

    /**
     * Sets the receiver of the message.
     *
     * @param int $receiverId The receiver's identifier.
     */
    public function setReceiver(int $receiverId)
    {
        if (!$receiverId) {
            throw new \DomainException("The identifier must be a non-zero number.");
        }
        $this->receiverId = $receiverId;

        return $this;
    }

    /**
     * Sets the body of the message.
     */
    public function setBody(string $body)
    {
        $this->body = $this->checkBody($body);
        $this->setCreatedAt(date('c'));

        return $this;
    }

    /**
     * Sets the date and time at which the message was created and sent.
     */
    public function setCreatedAt(string $timestamp)
    {
        $this->createdAt = $this->validateTimestamp($timestamp);

        return $this;
    }

    /**
     * Sets the status of the message.
     */
    public function setStatus(Status $status)
    {
        $this->status = $status;

        return $this;
    }


    /**
     * Retrieves the sender of the message.
     */
    public function getSender()
    {
        return Persistence::exStudentRepository()->findById($this->senderId);
    }

    /**
     * Retrieves the receiver of the message.
     */
    public function getReceiver()
    {
        return Persistence::exStudentRepository()->findById($this->receiverId);
    }

    /**
     * Retrieves the body of the message.
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Retrieves the timestamp of the message's creation.
     */
    public function createdAt()
    {
        return new \DateTime($this->createdAt);
    }

    /**
     * Retrieves the status of the message.
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Determines if the message was read.
     */
    public function isRead()
    {
        return ($this->status == Status::READ);
    }

    private function checkBody(string $body)
    {
        if (!$body) {
            throw new \LengthException("The body is required.");
        }
        return $body;
    }
}

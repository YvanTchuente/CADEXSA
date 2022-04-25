<?php

declare(strict_types=1);

namespace Application\Membership\Chats;

/**
 * Describes a chat
 */
class Chat
{
    /**
     * @var int|null
     */
    private $ID;

    /**
     * @var int|null
     */
    private $senderID;

    /**
     * @var int|null
     */
    private $receiverID;

    /**
     * @var string|null
     */
    private $message;

    /**
     * @var string|null
     */
    private $timestamp;

    /**
     * @var ChatStatus
     */
    private $status;

    public function __construct(
        int $ID = null,
        int $senderID = null,
        int $receiverID = null,
        string $message = null,
        string $timestamp = null,
        ChatStatus $status = ChatStatus::UNREAD
    ) {
        $this->ID = $ID;
        $this->senderID = $senderID;
        $this->receiverID = $receiverID;
        $this->message = $message;
        $this->timestamp = $timestamp;
        $this->status = $status;
    }

    /**
     * Sets the ID of the chat
     *
     * @param integer $ID
     */
    public function setID(int $ID)
    {
        if (!$ID) {
            throw new \DomainException("Invalid ID");
        }
        $this->ID = $ID;
        return $this;
    }

    /**
     * Sets the ID of the sender of the chat
     *
     * @param integer $senderID
     */
    public function setSenderID(int $senderID)
    {
        if (!$senderID) {
            throw new \DomainException("Invalid ID");
        }
        $this->senderID = $senderID;
        return $this;
    }

    /**
     * Sets the ID of the receiver of the chat
     *
     * @param integer $receiverID
     */
    public function setReceiverID(int $receiverID)
    {
        if (!$receiverID) {
            throw new \DomainException("Invalid ID");
        }
        $this->receiverID = $receiverID;
        return $this;
    }

    /**
     * Sets the message of the chat
     *
     * @param string $message
     */
    public function setMessage(string $message)
    {
        if (empty($message)) {
            throw new \DomainException("Invalid argument");
        }
        $this->message = $message;
        return $this;
    }

    /**
     * Sets the date and time of when the chat was sent
     *
     * @param string $timestamp
     */
    public function setTimestamp(string $timestamp)
    {
        if (empty($timestamp) || !strtotime($timestamp)) {
            throw new \DomainException("Invalid timestamp");
        }
        $this->timestamp = $timestamp;
        return $this;
    }

    /**
     * Sets the status of the chat
     *
     * @param ChatStatus $status The status of the chat
     **/
    public function SetStatus(ChatStatus $status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Retrieves the ID of the chat
     */
    public function getID()
    {
        return $this->ID;
    }

    /**
     * Retrieves the ID of sender  of the chat
     */
    public function getSenderID()
    {
        return $this->senderID;
    }

    /**
     * Retrieves the ID of receiver of the chat
     */
    public function getReceiverID()
    {
        return $this->receiverID;
    }


    /**
     * Retrieves the message of the chat
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Retrieves the date and time of when the chat was sent
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Return the status of chat
     *
     * @return string A case of the ChatStatus enumeration
     */
    public function getStatus()
    {
        $status = ucfirst(strtolower($this->status->name));
        return $status;
    }
}

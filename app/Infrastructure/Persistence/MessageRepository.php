<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure\Persistence;

use Cadexsa\Domain\Model\Message\Message;
use Cadexsa\Domain\Model\Message\MissingMessage;
use Cadexsa\Infrastructure\Persistence\Criteria;
use Cadexsa\Infrastructure\Persistence\Contracts\Criteria as CriteriaContract;



class MessageRepository extends Repository
{
    /**
     * Finds a message by its identifier.
     *
     * @param integer $messageId A message's identifier.
     * @return Message The message.
     */
    public function findById(int $messageId): Message
    {
        return $this->selectMatch(Criteria::equal('id', $messageId)) ?? new MissingMessage;
    }

    /**
     * Retrieves the chat between two given ex-students.
     *
     * @param int $exStudentId1 An ex-student's identifier.
     * @param int $exStudentId2 Another ex-student's identifier.
     * 
     * @return Message[] The chat.
     */
    public function getChat(int $exStudentId1, int $exStudentId2): array
    {
        $criteria1 = new DisjunctionCriteria(Criteria::equal('senderId', $exStudentId1), Criteria::equal('receiverId', $exStudentId2));
        $criteria2 = new DisjunctionCriteria(Criteria::equal('receiverId', $exStudentId1), Criteria::equal('senderId', $exStudentId2));
        $criteria = new ConjunctionCriteria($criteria1, $criteria2);
        $chat = $this->selectMatching($criteria);
        return $chat;
    }

    /**
     * Selects the first message matching a given criteria.
     *
     * @param CriteriaContract $criteria A selection criteria.
     * @return Message The message.
     */
    public function selectMatch(CriteriaContract $criteria): Message
    {
        return $this->strategy->selectMatching($criteria, $this)[0] ?? new MissingMessage;
    }

    /**
     * Selects messages matching a given criteria.
     * 
     * @param CriteriaContract $criteria A selection criteria.
     * 
     * @return Message[] A collection of messages
     */
    public function selectMatching(CriteriaContract $criteria): array
    {
        return $this->strategy->selectMatching($criteria, $this);
    }


    /**
     * Adds a message to the repository.
     *
     * @param Message $message The message.
     */
    public function add(Message $message)
    {
        $this->strategy->add($message);
    }

    /**
     * Removes a message from the repository.
     *
     * @param Message $message The message.
     */
    public function remove(Message $message)
    {
        $this->strategy->remove($message);
    }

    /**
     * Retrieves all messages.
     * 
     * @return Message[] All messages.
     */
    public function all(): array
    {
        return $this->strategy->all($this);
    }

    public function getEntityClass(): string
    {
        return MapperRegistry::getMapper(Message::class)->getDataMap()->getEntityClass();
    }
}

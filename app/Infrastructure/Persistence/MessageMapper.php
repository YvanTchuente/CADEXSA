<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure\Persistence;

use Cadexsa\Domain\Model\INull;
use Cadexsa\Domain\Model\Entity;
use Cadexsa\Domain\Message\Message;
use Cadexsa\Domain\Factories\MessageFactory;
use Cadexsa\Infrastructure\Contracts\Encrypter;

class MessageMapper extends Mapper
{
    protected function getEntityName(): string
    {
        return 'Message';
    }

    public function __construct(private Encrypter $encrypter)
    {
        parent::__construct();
    }

    protected function doLoad(array $resultSet)
    {
        $messageFactory = new MessageFactory($this->dataMap, $this->encrypter);
        $message = $messageFactory->reconstitute($resultSet);
        return $message;
    }

    /**
     * @param Message $entity
     */
    protected function doValidateContent($entity)
    {
        switch (true) {
            case ($entity->getSender() instanceof INull):
                throw new \DomainException("Invalid message sender identifier.");
                break;
            case ($entity->getReceiver() instanceof INull):
                throw new \DomainException("Invalid message receiver identifier.");
                break;
            case (!$entity->getBody()):
                throw new \DomainException('Invalid message body.');
                break;
            case (!$entity->createdAt()):
                throw new \DomainException('Invalid message creation timestamp.');
                break;
        }
    }

    protected function bindParams(Entity $subject, \PDOStatement &$stmt)
    {
        parent::bindParams($subject, $stmt);
        $body_column = $this->dataMap->getColumnMaps()['body'];
        $columnName = $body_column->getColumnName();
        $fieldValue = $this->encrypter->encrypt($body_column->getFieldValue($subject), config('app.key'));
        $stmt->bindValue($columnName, $fieldValue);
    }
}

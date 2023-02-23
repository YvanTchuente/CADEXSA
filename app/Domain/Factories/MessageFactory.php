<?php

declare(strict_types=1);

namespace Cadexsa\Domain\Factories;

use Cadexsa\Domain\Model\Message\Status;
use Cadexsa\Domain\Model\Message\Message;
use Cadexsa\Infrastructure\Persistence\DataMap;
use Cadexsa\Infrastructure\Contracts\Encrypter;

class MessageFactory extends EntityFactory
{
    public function __construct(DataMap $dataMap, private Encrypter $encrypter)
    {
        parent::__construct($dataMap);
    }

    /**
     * Creates a chat message.
     */
    public static function create(int $senderId, int $receiverId, string $body): Message
    {
        $now = date('Y-m-d H:i:s');
        $id = app()->IdGenerator()->generateId();
        $message = new Message($id, $senderId, $receiverId, $body);

        return $message;
    }

    /**
     * Reconstitutes a chat message from its stored representation.
     * 
     * @param array $resultSet An associative array of record data.
     */
    public function reconstitute(array $resultSet): Message
    {
        $this->validateResults($resultSet);
        extract($resultSet);
        $body = $this->encrypter->decrypt($body, config('app.key'));
        $status = Status::from((int) $status);

        // Reconstitute
        $message = new Message($id, $sender_id, $receiver_id, $body, $status, $created_at);

        return $message;
    }
}

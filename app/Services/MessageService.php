<?php

declare(strict_types=1);

namespace Cadexsa\Services;

use Cadexsa\Domain\Model\Persistence;
use Cadexsa\Domain\Model\Message\Message;
use Cadexsa\Domain\Factories\MessageFactory;

class MessageService
{
    public function createMessage(int $senderId, int $receiverId, string $body): Message
    {
        $message = MessageFactory::create($senderId, $receiverId, $body);
        Persistence::messageRepository()->add($message);
        return $message;
    }
}

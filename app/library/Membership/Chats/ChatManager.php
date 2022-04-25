<?php

declare(strict_types=1);

namespace Application\Membership\Chats;

use Application\Database\{
    Connector,
    ConnectionAware,
    ConnectionTrait,
};
use Application\Security\{
    Securer,
    Decrypter,
    Encrypter,
    SecurerAware,
    SecurerAwareTrait
};
use Application\CMS\Manager;
use Application\CMS\DeleteItemTrait;
use Application\CMS\ItemExistsTrait;

/**
 * Manages chat messages
 */
class ChatManager implements ConnectionAware, SecurerAware, Manager
{
    private const TABLE = "chats";

    public function __construct(
        Connector $connector,
        Encrypter $encrypter = new Securer(),
        Decrypter $decrypter = new Securer()
    ) {
        $this->setConnector($connector);
        $this->setEncrypter($encrypter);
        $this->setDecrypter($decrypter);
    }

    use ConnectionTrait;

    use SecurerAwareTrait;

    /**
     * Saves a chat message
     * 
     * Saves a chat message to persistent storage and return its ID
     * 
     * @return int ID of the stored chat
     * 
     * @throws \RuntimeException
     **/
    public function save(Chat $chat)
    {
        if (!$chat->getSenderID() || !$chat->getReceiverID() || !$chat->getMessage() || !$chat->getTimestamp()) {
            throw new \RuntimeException("Invalid chat");
        }
        $sender = $chat->getSenderID();
        $receiver = $chat->getReceiverID();
        $msg = $chat->getMessage();
        $timestamp = $chat->getTimestamp();
        $encryption = $this->encrypter->encrypt($msg);
        $connection = $this->connector->getConnection();
        $insert_sql = "INSERT INTO " . self::TABLE . " (senderID, receiverID, messageText, message_key, iv, timestamp) VALUES (?,?,?,?,?,?)";
        $stmt = $connection->prepare($insert_sql);
        $has_inserted = $stmt->execute([$sender, $receiver, $encryption['cipher'], $encryption['key'], $encryption['iv'], $timestamp]);
        if (!$has_inserted) {
            throw new \RuntimeException("Could not store the chat");
        }
        $chatID = (int) $connection->lastInsertId();
        return $chatID;
    }

    /**
     * Retrieves the conversation between two members
     * 
     * Retrieves the chats exchanged between two members and returns an array of Chat instances.
     * If there is no chat exchanged, returns an empty array
     * 
     * @param int $senderID The ID of member requesting for the conversation
     * @param int $receiverID The ID of member whose conversation with is requested
     * 
     * @return \Application\Membership\Chats\Chat[]
     * 
     * @throws \LogicException In case Sender ID and Receiver ID are the same
     **/
    public function getConversation(int $senderID, int $receiverID)
    {
        if ($senderID === $receiverID) {
            throw new \LogicException("Sender's ID and Receiver's ID parameters are the same");
        }
        $conversation = [];
        $sql = "SELECT * FROM chats WHERE (senderID='$senderID' AND receiverID='$receiverID') OR (senderID='$receiverID' AND receiverID='$senderID')";
        $result = $this->connector->getConnection()->query($sql);
        while ($row = $result->fetch(\PDO::FETCH_ASSOC)) {
            $cipherText = $row['messageText'];
            $key = $row['message_key'];
            $iv = $row['iv'];
            $message = $this->decrypter->decrypt($cipherText, $key, $iv);
            $chat_item = (new Chat())->setSenderID((int) $row['senderID'])
                ->setReceiverID((int) $row['receiverID'])
                ->setMessage($message)
                ->setTimestamp($row['timestamp']);
            $chat_item->setID((int)$row['ID']);
            $conversation[] = $chat_item;
            if ($row['receiverID'] === $receiverID) {
                $this->markAsRead($row['ID']);
            }
        }
        return $conversation;
    }

    public function get(int $ID): Chat
    {
        $query = $this->connector->getConnection()->query("SELECT * FROM " . self::TABLE . " WHERE ID = '$ID'");
        $data = $query->fetch(\PDO::FETCH_ASSOC);
        if (!$data) {
            throw new \RuntimeException(sprintf("The item identified by ID %d does not exist", $ID));
        }
        $message = $this->decrypter->decrypt($data['messageText'], $data['message_key'], $data['iv']);
        $chat = new Chat($data['ID'], $data['senderID'], $data['receiverID'], $message, $data['timestamp'], ChatStatus::from((int)$data['status']));
        return $chat;
    }

    /**
     * @return Chat[]
     */
    public function list(int $n = 0, int $offset = null, bool $sort = true)
    {
        $chats = [];
        $sql = "SELECT ID FROM " . self::TABLE;
        if ($sort) {
            $sql .= " ORDER BY timestamp DESC";
        }
        if ($n > 0) {
            $sql .= " LIMIT $n";
        }
        if (isset($offset)) {
            $sql .= " OFFSET $offset";
        }
        $query = $this->connector->getConnection()->query($sql);
        $res = $query->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($res as $row) {
            foreach ($row as $column) {
                $chats[] = $this->get((int)$column);
            }
        }
        return $chats;
    }

    use ItemExistsTrait;

    use DeleteItemTrait;

    /**
     * Marks a chat as being already read
     *
     * @param integer $ID The ID of the chat
     * 
     * @return bool
     */
    public function markAsRead(int $ID)
    {
        $exists = $this->exists($ID);
        if (!$exists) {
            throw new \RuntimeException(sprintf("The chat referenced by ID of %d does not exit", $ID));
        }
        $stmt = $this->connector->getConnection()->query("SELECT status FROM chats WHERE ID = '$ID'");
        $status = (int) $stmt->fetch()['status'];
        if ($status === 1) {
            return true;
        }
        $has_updated = (bool) $this->connector->getConnection()->query("UPDATE chats SET status = '1' WHERE ID = '$ID'");
        return $has_updated;
    }
}

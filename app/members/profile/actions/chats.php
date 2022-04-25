<?php

/**
 * Chats server
 * 
 * Handle instant messages exchanged between members
 * 
 * @author Yvan Tchuente <yvantchuente@gmail.com>
 */

require_once dirname(__DIR__, 3) . "/config/index.php";

set_time_limit(0);
ob_implicit_flush();

use Application\Database\Connection;
use Application\Network\WebSocketServer;
use Application\Membership\MemberManager;
use Application\Membership\Chats\{Chat, ChatManager};

$address = gethostbyname("localhost");
$port = 5000;

// Create a new websocket server
$server = new WebSocketServer($address, $port, "localhost", ['/members/profile/actions/chats.php']);

/** @var Socket Listening server socket */
$server_socket = $server->getSocket();

$waiting_list = array($server_socket);

/** @var array Stores connected client sockets */
$clients = [];

// Daemon process
while (true) {
    // Read array contains potential connecting clients
    $read = array_merge($waiting_list, $clients);
    // Return sockets containing data available for reading in 'read' array
    $readable_sockets = socket_select($read, $null, $null, 0);
    if ($readable_sockets < 1) {
        continue;
    }

    // Check for incoming connections
    if (in_array($server_socket, $read)) {
        $client_socket = socket_accept($server_socket);
        $client_handshake = socket_read($client_socket, 1024); // Read data sent from the client socket
        $isConnected = $server->connect($client_handshake, $client_socket);
        // If the connection is established
        if ($isConnected) {
            $queryParams = $server->getQueryParams($client_handshake);
            if (isset($queryParams)) {
                $clients[$queryParams['member']] = $client_socket; // Add socket to client array identified by the user ID
            } else {
                $clients[] = $client_socket; // Add the socket to client array
            }
        }
        // Make room for new incomming sockets
        $index = array_search($server_socket, $read);
        unset($read[$index]);
    }

    $connected_sockets = $read;
    // Loop through all connected sockets
    foreach ($connected_sockets as $connected_socket) {
        // Keep receiving data from the current connected socket until no data is received
        while (socket_recv($connected_socket, $buffer, 1024, 0) >= 1) {
            $received_text = $server->decode($buffer); // Decode data from the client
            $msg = json_decode($received_text);
            $action = $msg->action;
            $chatManager = new ChatManager(Connection::Instance());
            // Depending on the value of $actions, switch to correct actions to be done
            switch ($action) {
                case 'get_chat_user':
                    $requestedUser = $msg->requestedUser;
                    $requester = $msg->requester;
                    // Retrieve the requested chat user's infos and conversation with the requester
                    $chatUser = MemberManager::Instance()->getMember((int) $requestedUser);
                    $status = MemberManager::Instance()->getStatus($chatUser->getID());
                    $conversation = get_chat_conversation($chatManager, $requestedUser, $requester);
                    // Encapsulate the results into an array
                    $chat_user_info = array(
                        'type' => 'chat_user_info',
                        'avatar' => $chatUser->getPicture(),
                        'username' => '<a href="/members/profiles/' . strtolower($chatUser->getUserName()) . '" target="_blank">' . $chatUser->getName() . '</a>',
                        'status' => $status,
                        'conversation' => $conversation
                    );
                    $content = json_encode($chat_user_info);
                    $server->send($connected_socket, 'text', $content);
                    break;

                case 'post_chat':
                    $sender = $msg->sender;
                    $receiver = $msg->receiver;
                    $message = $msg->message;
                    $timestamp = date("Y-m-d H:i:s");
                    $chat = (new Chat())->setSenderID($sender)
                                        ->setReceiverID($receiver)
                                        ->setMessage($message)
                                        ->setTimestamp($timestamp);
                    // Stores the chat in the database and returns it for display 
                    $chatID = $chatManager->save($chat);
                    $chat->setID($chatID);
                    $avatar = MemberManager::Instance()->getMember($chat->getSenderID())->getPicture();
                    $timestamp = date("H:i A", strtotime($chat->getTimestamp())) . " | " . date("l", strtotime($chat->getTimestamp()));
                    $newChat = array(
                        'type' => 'new_chat',
                        'ID' => $chat->getID(),
                        'sender' => $chat->getSenderID(),
                        'receiver' => $chat->getReceiverID(),
                        'avatar' => $avatar,
                        'message' => $chat->getMessage(),
                        'timestamp' => $timestamp
                    );
                    $content = json_encode($newChat);
                    $server->send($connected_socket, 'text', $content); // Sends the message to the sender
                    if (isset($clients[$newChat['receiver']])) {
                        $receiver = $clients[$newChat['receiver']];
                        $server->send($receiver, 'text', $content); // Sends the message to the receiving socket
                    }
                    break;

                case 'update_last_activity':
                    $connection = Connection::Instance()->getConnection();
                    $memberID = $msg->member;
                    $query = "UPDATE online_members SET last_activity = current_timestamp() WHERE memberID='$memberID'";
                    $connection->query($query);
                    break;

                case 'update_typing_status':
                    $memberID = $msg->member;
                    $correspondentID = $msg->correspondent;
                    $value = (int) $msg->value;
                    // Notify the correspondent that this member is typing or has stopped typing
                    switch ($value) {
                        case 1:
                            $status = 'Typing...';
                            break;
                        case 0:
                            $status = 'Online';
                            break;
                    }
                    $content = json_encode(array(
                        'type' => 'member_status',
                        'member' => $memberID,
                        'status' => $status
                    ));
                    if (isset($clients[$correspondentID])) {
                        $correspondent = $clients[$correspondentID];
                        $server->send($correspondent, 'text', $content);
                    }
                    break;
            }
            break 2; //exist this loop
        }

        // Check if the client is disconnected
        $buffer = socket_read($connected_socket, 1024, PHP_NORMAL_READ);
        if ($buffer === false) {
            // Remove the client's socket from the list of connected clients
            $server->disconnect($connected_socket, 1001);
            $index = array_search($connected_socket, $clients);
            unset($clients[$index]);
        }
    }
}
// Closes the client's socket
socket_close($client);

/**
 * Retrieves the conversation between two members
 * 
 * @param ChatManager $ChatManager A ChatManager instance
 * @param int $user2 ID of member whose conversation with is requested
 * @param int $user1 ID of member requesting for the conversation
 * @return array|string
 **/
function get_chat_conversation(ChatManager $ChatManager, int $user2, int $user1)
{
    if ($user1 == $user2) {
        throw new InvalidArgumentException("User1 ID and User2 ID parameters are the same");
    }
    $conversation = [];
    $chats = $ChatManager->getConversation($user2, $user1);
    if (isset($chats) && count($chats) > 0) {
        foreach ($chats as $chat) {
            $avatar = MemberManager::Instance()->getMember($chat->getSenderID())->getPicture();
            $message = $chat->getMessage();
            $timestamp = date("H:i A", strtotime($chat->getTimestamp())) . " | " . date("l", strtotime($chat->getTimestamp()));
            if ($user1 == $chat->getSenderID()) {
                $chat_item = '<div class="my_chat"><div><p>' .
                    $message .
                    '</p><span class="time">' .
                    $timestamp .
                    '</span></div><img src="' .
                    $avatar .
                    '"/>';
            } elseif ($user1 == $chat->getReceiverID()) {
                $chat_item = '<div class="client_chat"><img src="' .
                    $avatar .
                    '"/><div><p>' .
                    $message .
                    '</p><span class="time">' .
                    $timestamp .
                    '</span></div></div>';
            }
            $conversation[] = $chat_item;
        }
        return $conversation;
    } else {
        return '<div id="chat_alert"><span>No conversation</span></div>';
    }
}

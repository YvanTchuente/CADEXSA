<?php

/**
 * Handles instant messages exchanged between members in real-time.
 */

require __DIR__ . "/../../vendor/autoload.php";

require_once __DIR__ . "/../bootstrap/app.php";

use Cadexsa\Services\Registry;
use Cadexsa\Domain\Model\Persistence;
use Tym\Websocket\Server as WebSocketServer;
use Cadexsa\Infrastructure\Transaction\TransactionManager;

set_time_limit(0);
ob_implicit_flush();

// Instantiate a websocket server instance
$address = gethostbyname("localhost");
$server = new WebSocketServer($address, 5000, "localhost", ['/services/chat.php']);

$server->start();

// Initialize the waiting list and client list
$server_socket = $server->getSocket();
$waiting_list = array($server_socket);
$clients = []; // Stores connected client sockets

TransactionManager::beginTransaction();

// Daemon process
while (true) {
    $read = array_merge($waiting_list, $clients); // Contains potential clients
    $readable_socket_count = socket_select($read, $null, $null, 0); // Select sockets with readable data
    if ($readable_socket_count < 1) {
        continue;
    }

    // Check for incoming connection requests.
    if (in_array($server_socket, $read)) {
        $client_socket = socket_accept($server_socket);
        $client_handshake = socket_read($client_socket, 1024); // Retrieve the client's opening handshake
        $connected = $server->connect($client_handshake, $client_socket);

        if ($connected) {
            $queryParams = $server->getQueryParams($client_handshake);
            if (isset($queryParams)) {
                $clients[$queryParams['exstudent']] = $client_socket; // Add the socket to the clients list
            } else {
                $clients[] = $client_socket; // Add the socket to the clients list
            }
        }

        // Make room for new incoming sockets
        $index = array_search($server_socket, $read);
        unset($read[$index]);
    }

    $connected_sockets = $read;

    foreach ($connected_sockets as $connected_socket) {
        // Receive data from the current socket until no more data is available
        while (socket_recv($connected_socket, $data, 2048, 0) >= 1) {
            $parsedData = $server->decode($data);
            $msg = json_decode($parsedData);

            switch ($msg->action) {
                case 'get_chat_user':
                    $targetExstudentId = (int) $msg->targetExstudentId;
                    $requestingExstudentId = (int) $msg->requestingExstudentId;

                    $exstudent = Persistence::exStudentRepository()->findById($targetExstudentId);
                    $state = $exstudent->state()->label();
                    $chat = [];
                    foreach (Persistence::messageRepository()->getChat($targetExstudentId, $requestingExstudentId) as $message) {
                        $data = array(
                            'id' => $message->getId(),
                            'sender' => $message->getSender()->getId(),
                            'receiver' => $message->getReceiver()->getId(),
                            'avatar' => $message->getSender()->getAvatar(),
                            'body' => $message->getBody(),
                            'createdAt' => $message->createdAt()->format("H:i A") . " | " . $message->createdAt()->format("l")
                        );
                        $chat[] = $data;
                    }
                    $data = array(
                        'type' => 'chat_user_data',
                        'avatar' => $exstudent->getAvatar(),
                        'username' => '<a href="/exstudents/' . strtolower($exstudent->getUserName()) . '" target="_blank">' . $exstudent->getName() . '</a>',
                        'state' => $state,
                        'chat' => $chat
                    );
                    $server->send($connected_socket, 'text', json_encode($data));
                    break;

                case 'post_message':
                    $message = Registry::messageService()->createMessage((int) $msg->sender, (int)$msg->receiver, $msg->message);
                    $avatar = $message->getSender()->getAvatar();
                    $createdAt = $message->createdAt()->format("H:i A") . " | " . $message->createdAt()->format("l");
                    $data = array(
                        'type' => 'new_message',
                        'id' => $message->getId(),
                        'sender' => $message->getSender()->getId(),
                        'receiver' => $message->getReceiver()->getId(),
                        'avatar' => $avatar,
                        'body' => $message->getBody(),
                        'createdAt' => $createdAt
                    );
                    $server->send($connected_socket, 'text', json_encode($data)); // Sends the message to the sender
                    if (isset($clients[$data['receiver']])) {
                        $receiver = $clients[$data['receiver']];
                        $server->send($receiver, 'text', json_encode($data)); // Sends the message to the receiving socket
                    }
                    TransactionManager::getCurrentTransaction()->commit();
                    break;

                case 'update_last_activity':
                    $connection = app()->database->getConnection();
                    $exStudentId = $msg->exstudent;
                    $query = "UPDATE online_members SET last_activity = current_timestamp() WHERE exStudentId='$exStudentId'";
                    $connection->pdo->query($query);
                    break;

                case 'update_typing_status':
                    $exStudentId = $msg->exstudent;
                    $correspondentId = $msg->correspondent;
                    $status = (int) $msg->status;
                    // Notify the correspondent that this exstudent is typing or has stopped typing
                    switch ($status) {
                        case 1:
                            $status = 'Typing...';
                            break;
                        case 0:
                            $status = 'Online';
                            break;
                    }
                    $data = array(
                        'type' => 'member_status',
                        'exstudent' => $exStudentId,
                        'status' => $status
                    );
                    if (isset($clients[$correspondentId])) {
                        $correspondent = $clients[$correspondentId];
                        $server->send($correspondent, 'text', json_encode($data));
                    }
                    break;
            }

            break 2; //exist this loop
        }

        // Check if the client host disconnected
        $data = socket_read($connected_socket, 1024, PHP_NORMAL_READ);
        if ($data === false) {
            // Remove the client host's socket from the client list
            $server->disconnect($connected_socket, 1001);
            $index = array_search($connected_socket, $clients);
            unset($clients[$index]);
        }
    }
}

socket_close($server_socket);
app()->database->disconnect();
TransactionManager::commit();

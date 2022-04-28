<?php

/**
 * Push chat-related updates to all connected chatting members
 * 
 * @author Yvan Tchuente <yvantchuentel@gmail.com>
 */

require_once dirname(__DIR__, 3) . "/config/index.php";

set_time_limit(0);
ob_implicit_flush();

use Application\Network\WebSocketServer;
use Application\Membership\MemberManager;
use Application\DateTime\ChatTimeDuration;

$address = gethostbyname("localhost");
$port = 5050;

// Create a new websocket server
$server = new WebSocketServer($address, $port, "localhost", ['/members/profile/actions/chats-update.php']);

/** @var socket Listening server socket */
$server_socket = $server->getSocket();

$waiting_list = array($server_socket);

/** @var array Stores connected client sockets */
$clients = [];

while (true) {
    // Read array contains potential connecting clients
    $read = array_merge($waiting_list, $clients);
    // Return sockets containing data available for reading in 'read' array
    $readable_sockets = socket_select($read, $null, $null, 0);
    if ($readable_sockets < 1) {
        continue;
    }

    // Check for incoming socket connection
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
        // Make room for new socket
        $index = array_search($server_socket, $read);
        unset($read[$index]);
    }

    if (count($clients) > 0) {
        // Push server messages to all clients
        foreach ($clients as $client) {
            $memberID = array_search($client, $clients);
            $states = [];
            $n = 0;
            foreach (MemberManager::Instance()->getMembers() as $chatUser) {
                if ($memberID == $chatUser->getID()) {
                    continue;
                }
                $timeDuration = new ChatTimeDuration();
                $state = MemberManager::Instance()->getState($chatUser->getID(), $timeDuration);
                $users_states[] = array('n' => $n, 'memberID' => $chatUser->getID(), 'memberName' => $chatUser->getName(), 'status' => ucfirst($state['status']), 'lastSeen' => $state['lastSeen']);
                $n++;
            }
            $states = array('type' => 'chat_users_states', 'states' => $users_states);
            unset($users_states); // Reset the states
            $content = json_encode($states);
            $server->send($client, 'text', $content);
        }
        sleep(1);
    }
}

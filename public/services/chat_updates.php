<?php

/**
 * Push chat-related updates to all connected chatting members
 */

require __DIR__ . "/../../vendor/autoload.php";

require_once __DIR__ . "/../bootstrap/app.php";

use Cadexsa\Domain\ServiceRegistry;
use Cadexsa\Domain\Model\Persistence;
use Tym\Websocket\Server as WebSocketServer;
use Cadexsa\Infrastructure\Transaction\TransactionManager;

set_time_limit(0);
ob_implicit_flush();

// Instantiate a websocket server instance
$address = gethostbyname("localhost");
$server = new WebSocketServer($address, 5050, "localhost", ['/services/chat_updates.php']);

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

    // Check for incoming connections
    if (in_array($server_socket, $read)) {
        $client_socket = socket_accept($server_socket);
        $client_handshake = socket_read($client_socket, 1024); // Read data sent from the client socket
        $isConnected = $server->connect($client_handshake, $client_socket);
        if ($isConnected) {
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

    if (count($clients) > 0) {
        // Push server messages to all clients
        foreach ($clients as $client) {
            $exStudentId = array_search($client, $clients);
            $states = [];
            $n = 0;
            foreach (Persistence::exStudentRepository()->all() as $chatUser) {
                if ($exStudentId == $chatUser->getId()) {
                    continue;
                }
                $state = (ServiceRegistry::authenticationService()->check($chatUser->getUsername())) ? 'Online' : 'Offline';
                $lastSeen = ServiceRegistry::timeIntervalCalculator()->elapsedTimeSinceLastActivity($chatUser->getId());
                $users_states[] = array('n' => $n, 'exStudentId' => $chatUser->getId(), 'exstudentName' => $chatUser->getName(), 'state' => $state, 'lastSeen' => $lastSeen);
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

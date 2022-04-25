<?php

/**
 * Web Socket Server class
 * 
 * @author Yvan Tchuente <yvantchuente@gmail.com>
 * @copyright 2022 Yvan Tchuente
 */

declare(strict_types=1);

namespace Application\Network;

/**
 * Web socket server.
 * 
 * @author Yvan Tchuente <yvantchuente@gmail.com>
 */
class WebSocketServer
{
    /**
     * The Globally Unique Identifier
     * 
     * @var string
     */
    private const GUID =  "258EAFA5-E914-47DA-95CA-C5AB0DC85B11";

    /**
     * Socket
     * 
     * @var Socket
     */
    private $socket;

    /**
     * Hostname
     * 
     * @var string
     */
    private $serverName;

    /**
     * List of endpoints served by the server
     * 
     * @var string[]
     */
    private $services;

    /**
     * List of origin URIs from which to incoming requests shall be accepted
     *
     * @var array
     */
    private $valid_origins;

    /**
     * Initializes the server
     * 
     * @param string $address IP address (in dotted-quad notation) to bind to the server
     * @param int $port Port on which the server shall listen for incoming connections
     * @param string $serverName Server's hostname
     * @param string[] $services List of services provided by the server, these are the the endpoints served by the server
     * @param string[] $valid_origins List of origin URIs from which to incoming requests shall be accepted
     * 
     * @throws \InvalidArgumentException For any invalid argument
     **/
    public function __construct(
        string $address,
        int $port,
        string $serverName,
        array $services,
        array $valid_origins = []
    ) {
        if (!$address || !$port || !$serverName || !$services) {
            throw new \InvalidArgumentException("Some argument(s) is/are empty");
        }
        if (!preg_match('/(\d{1,3}(\b|\.)){4}/', $address)) {
            throw new \InvalidArgumentException("Invalid address");
        }
        if (!$port >= 1023 && !$port <= 65536) {
            throw new \InvalidArgumentException("Do not accept well-known ports");
        }
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);
        socket_bind($socket, $address, $port);
        socket_listen($socket);
        $this->socket = $socket;
        $this->serverName = $serverName;
        $this->services = $services;
        if ($valid_origins) {
            foreach ($valid_origins as $origin) {
                if (gettype($origin) == 'string') {
                    throw new \InvalidArgumentException("Invalid list of origin URIs");
                }
            }
        }
        $this->valid_origins = $valid_origins;
    }

    /**
     * Retrieves the underlying socket
     * 
     * @return \Socket
     **/
    public function getSocket()
    {
        return $this->socket;
    }

    /**
     * Sets the list of origin URIs from which incoming requests shall be accepted
     *
     * @param string[] $origins
     */
    public function setValidOrigins(array $origins)
    {
        if ($origins) {
            throw new \InvalidArgumentException("Invalid list of origin URIs");
        }
        foreach ($origins as $origin) {
            if (gettype($origin) == 'string') {
                throw new \InvalidArgumentException("Invalid list of origin URIs");
            }
        }
        $this->valid_origins = $origins;
    }

    /**
     * Establishes or rejects a websocket connection with a client socket in accordance with the Websocket protocol
     * 
     * Inspects the client's opening handshake and decide whether to complete the handshake 
     * to establish the connection or reject it and notifying the client by so doing
     * 
     * @param string $client_request Client's opening handshake;
     * @param \Socket $client_socket Client's socket
     * 
     * @return bool
     **/
    public function connect(string $client_request, \Socket $client_socket)
    {
        $headers = $this->getHeaders($client_request);
        $isAccepted = $this->verifyHandshake($headers);
        if (!$isAccepted) {
            $response = "HTTP/1.1 400 Bad Request\r\n" . "Connection: close\r\n";
            socket_write($client_socket, $response, strlen($response));
            return false;
        }
        $endpoint = $this->getRequestLine($client_request)['endpoint'];
        if (!$this->IsService($endpoint)) {
            $response = "HTTP/1.1 404 Not Found\r\n" . "Connection: close\r\n";
            socket_write($client_socket, $response, strlen($response));
            return false;
        }
        $origin = $headers['Origin'];
        switch (true) {
            case ($this->valid_origins && !$origin):
            case ($this->valid_origins && !in_array($origin, $this->valid_origins, true)):
                $response = "HTTP/1.1 403 Forbidden";
                socket_write($client_socket, $response, strlen($response));
                return false;
                break;
        }
        $secKey = $headers['Sec-WebSocket-Key'];
        $secAccept_key = base64_encode(pack(
            'H*',
            sha1($secKey . self::GUID)
        ));
        // Server handshake headers
        $upgrade_headers  = "HTTP/1.1 101 Switching Protocols\r\n" .
            "Upgrade: websocket\r\n" .
            "Connection: Upgrade\r\n" .
            "Sec-WebSocket-Version: 13\r\n" .
            "Sec-WebSocket-Accept:$secAccept_key\r\n\r\n";
        socket_write($client_socket, $upgrade_headers, strlen($upgrade_headers));
        return true;
    }

    /**
     * Performs a websocket closing handshake with a connected socket
     *
     * @param \Socket $client_socket The client socket
     * @param int $code The status code
     * @param string $reason The close reason
     * 
     * @return bool
     **/
    public function disconnect(\Socket $client_socket, int $code, string $reason = "")
    {
        $message = pack('I', $code) . $reason;
        $this->send($client_socket, 'close', $message);
        $this->close($client_socket);
    }

    /**
     * Performs a clean closure of the websocket connection with a client socket
     **/
    public function close(\Socket $client_socket)
    {
        socket_shutdown($client_socket);
        socket_close($client_socket);
    }

    /**
     * Sends a data frame to a connected socket.
     * 
     * Masks the data frame according to its type and sends it over the connection wire
     * to a client socket
     * 
     * @param \Socket $client Client socket
     * @param string $type Type of the data frame
     * @param string|\Stringable $data The data frame
     * 
     * @return bool
     * 
     * @throws \InvalidArgumentException If type is not a valid frame type.
     **/
    public function send(\Socket $client, string $type, string $data)
    {
        $encoded_data = $this->encode($type, $data);
        return (bool) socket_write($client, $encoded_data, strlen($encoded_data));
    }

    /**
     * Broadcasts data to all connected client sockets.
     * 
     * @param \Socket[] $clients An array of socket representing connected clients
     * @param string $type Type of the data frame
     * @param string $data The data
     **/
    public function broadcast(array $clients, string $type, string $data)
    {
        foreach ($clients as $client) {
            $this->send($client, $type, $data);
        }
    }

    /**
     * Frames data to send over the socket connection
     * 
     * Frames data to be sent through the underlying socket to one or more client(s)
     * 
     * @param string $type Type of the data
     * @param string|Stringable $data The data
     * 
     * @return string Masked data
     * 
     * @throws \InvalidArgumentException If type is not a valid frame type.
     **/
    public function encode(string $type, string|\Stringable $data)
    {
        if (!in_array($type, ['text', 'binary', 'close', 'ping', 'pong'])) {
            throw new \InvalidArgumentException("Invalid frame type");
        }
        $length = strlen($data);
        // Type of data frame
        switch ($type) {
            case 'text':
                $byte1 = 0x81; // 1000 0001
                break;
            case 'binary':
                $byte1 = 0x82; // 1000 0010
                break;
            case 'close':
                $byte1 = 0x88; // 1000 1000 
                break;
            case 'ping':
                $byte1 = 0x89; // 1000 1001 
                break;
            case 'pong':
                $byte1 = 0x8A; // 1000 1010
                break;
        }
        if ($length <= 125) {
            $header = pack('C*', $byte1, $length);
        } elseif ($length > 125 && $length < 65536) {
            $header = pack('CCn', $byte1, 126, $length);
        } elseif ($length >= 65536) {
            $header = pack('CCN', $byte1, 127, $length);
        }
        return $header . $data;
    }

    /**
     * Unmasks a data frame 
     * 
     * Unmasks a data frame sent over the socket connection
     * 
     * @param string $frame The masked data frame
     * 
     * @return string $data Unmasked data
     **/
    public function decode(string $frame)
    {
        $length = ord($frame[1]) & 127;
        if ($length == 126) {
            $masks = substr($frame, 4, 4);
            $data = substr($frame, 8);
        } elseif ($length == 127) {
            $masks = substr($frame, 10, 4);
            $data = substr($frame, 14);
        } else {
            $masks = substr($frame, 2, 4);
            $data = substr($frame, 6);
        }
        $frame = "";
        for ($i = 0; $i < strlen($data); ++$i) {
            $frame .= $data[$i] ^ $masks[$i % 4];
        }
        return $frame;
    }

    /**
     * Retrieves the request line of an HTTP request.
     * 
     * Retrieves the HTTP request's method, the endpoint and the HTTP version of the client's handshake.
     * 
     * @param string $client_request Client's handshake
     * 
     * @return array Associative array that contains three elements namely: method, endpoint and version
     * 
     * @throws \RuntimeException If an invalid HTTP method or version is found in the request line
     **/
    public function getRequestLine(string $client_request)
    {
        $requestLine = preg_split("/\r\n/", $client_request)[0];
        $parts = explode(" ", $requestLine);
        if (!preg_match('/^GET$/', $parts[0])) {
            throw new \RuntimeException("Invalid request: invalid HTTP request method. It must be a GET method");
        } elseif (!preg_match('/^HTTP\/\d\.\d$/', $parts[2])) {
            throw new \RuntimeException("Invalid request: invalid HTTP version");
        }
        return ['method' => $parts[0], 'endpoint' => $parts[1], 'version' => $parts[2]];
    }

    /**
     * Retrieves query paramaters if any present in the request URI.
     * 
     * @param string $client_request The client's handshake
     * 
     * @return array|null
     **/
    public function getQueryParams(string $client_request)
    {
        $requestLine = $this->getRequestLine($client_request);
        if (preg_match('/\?(\S+)=(.*)/', $requestLine['endpoint'], $matches)) {
            $query[$matches[1]] = $matches[2];
        }
        if (isset($query)) {
            return $query;
        } else {
            return null;
        }
    }

    /**
     * Retrieves the header fields of a request
     *
     * @param string $client_request
     * 
     * @return string[] An associative array of header values
     */
    private function getHeaders(string $client_request)
    {
        $lines = preg_split("/\r\n/", $client_request);
        // Client handshake headers
        foreach ($lines as $line) {
            $line = rtrim($line);
            if (preg_match('/(\S+): (.*)/', $line, $matches)) {
                $headers[$matches[1]] = $matches[2];
            }
        }
        return $headers;
    }

    /**
     * Determines whether the requested endpoint is served by the server
     * 
     * @param string $endpoint Websocket endpoint
     * 
     * @return bool
     */
    private function IsService(string $endpoint)
    {
        $isAccepted = false;
        foreach ($this->services as $service) {
            $service = preg_quote($service, '/');
            if (preg_match("/^$service(\?(\w+=\w?&?)+)?$/", $endpoint)) {
                $isAccepted = true;
            }
        }
        return $isAccepted;
    }

    /**
     * Accepts or refuses the client's handshake
     *
     * @param array $headers Associative array of header values of the handshake
     * 
     * @return bool
     */
    private function verifyHandshake(array $headers)
    {
        // Retrieve headers
        $host = $headers['Host'];
        $upgrade = $headers['Upgrade'];
        $connection = $headers['Connection'];
        $secKey = $headers['Sec-WebSocket-Key'];
        $secVersion = (int) $headers['Sec-WebSocket-Version'];

        $isOk = true;
        if (empty($host) || !preg_match("/" . $this->serverName . "/", $host)) {
            $isOk = false;
        }
        if (empty($upgrade) || !preg_match('/^websocket$/i', $upgrade)) {
            $isOk = false;
        }
        if (empty($connection) || !preg_match('/Upgrade/i', $connection)) {
            $isOk = false;
        }
        if (empty($secKey) || strlen(base64_decode($secKey)) !== 16) {
            $isOk = false;
        }
        if (empty($secVersion) || $secVersion !== 13) {
            $isOk = false;
        }
        return $isOk;
    }
}

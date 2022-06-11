<?php

/**
 * HTTP client class
 * 
 * Implements the PSR-18 Client Interface.
 * 
 * @author Yvan Tchuente <yvantchuente@gmail.com>
 * @copyright 2022 Yvan Tchuente
 */

declare(strict_types=1);

namespace Application\Network;

use Psr\Http\Message\{
    UriInterface,
    MessageInterface,
    RequestInterface,
    ResponseInterface,
    StreamFactoryInterface,
    RequestFactoryInterface,
    ResponseFactoryInterface,
};
use Psr\Http\Client\ClientInterface;

/**
 * HTTP client
 * 
 * @author Yvan Tchuente <yvantchuente@gmail.com>
 */
class Client implements ClientInterface
{
    /**
     * Valid HTTP methods
     * 
     * @var string
     */
    private const VALID_METHODS = '/^GET|HEAD|POST|PUT|DELETE|TRACE|CONNECT|OPTIONS$/i';

    /**
     * Default options for all requests
     * 
     * @var array
     */
    private const DEFAULT_OPTIONS = [
        CURLOPT_HEADER => true,
        CURLOPT_AUTOREFERER => true,
        CURLINFO_HEADER_OUT => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS
    ];

    /**
     * Valid client configuration settings
     * 
     * Contains the value type of each possible client configuration setting.
     * 
     * Each item (i.e the value type of a client configuration setting) of this array is
     * indexed by the name of the configuration setting.
     * 
     * Valid configuration settings include:
     * 
     * - enable_compression: (boolean) Whether or not to gzip compress the body of every HTTP request to send.
     * 
     * - enable_decompression: (boolean) Whether or not to gzip decompress the body of every HTTP response received.
     *                         If decompression is enabled, it will only decompress if the Content-Encoding header
     *                         value of the HTTP response is 'gzip'
     * 
     * - timeout: (int) The maximum amount of time (in seconds) to wait after which the request times out.
     * 
     * - connect_timeout: (int) The maximum amount of time (in seconds) to wait while trying to connect
     *                          to the host. Use 0 to wait indefinitely.
     * 
     * - max_redirects: (int) The maximum amount of HTTP redirections to follow.
     * 
     * - http_auth: (string) The HTTP authentication method to use. Possible values for these include:
     *                       basic, digest, ntlm, gssnegotiate and any (The client will poll the server for what method to use).
     * 
     * - default_protocol: (string) Default protocol to use if the request's URI is missing a scheme. Either http or https
     * 
     * @var array
     */
    private const CONFIGS = [
        'timeout' => 'integer',
        'http_auth' => 'string',
        'max_redirects' => 'integer',
        'default_protocol' => 'string',
        'connect_timeout' => 'integer',
        'enable_compression' => 'boolean',
        'enable_decompression' => 'boolean'
    ];

    /** 
     * @var \CurlHandle
     */
    private $curlHandle;

    /**
     * Client configuration settings
     *
     * @var string[]
     */
    protected $configs = [];

    /** 
     * Stream factory instance
     * 
     * @var StreamFactoryInterface
     */
    protected $streamFactory;

    /** 
     * HTTP request factory instance
     * 
     * @var RequestFactoryInterface
     * 
     */
    protected $requestFactory;

    /** 
     * HTTP response factory instance
     * 
     * @var ResponseFactoryInterface
     */
    protected $responseFactory;

    /**
     * This constructor accepts an associative array of client configuration settings
     * 
     * Valid configuration settings include:
     * 
     * - enable_compression: (boolean) Whether or not to gzip compress the body of every HTTP request to send.
     * 
     * - enable_decompression: (boolean) Whether or not to gzip decompress the body of every HTTP response received.
     *                         If decompression is enabled, it will only decompress if the Content-Encoding header
     *                         value of the HTTP response is 'gzip'
     * 
     * - timeout: (int) The maximum amount of time (in seconds) to wait after which the request times out.
     * 
     * - connect_timeout: (int) The maximum amount of time (in seconds) to wait while trying to connect
     *                          to the host. Use 0 to wait indefinitely.
     * 
     * - max_redirects: (int) The maximum amount of HTTP redirections to follow.
     * 
     * - http_auth: (string) The HTTP authentication method to use. Possible values for these include:
     *                       basic, digest, ntlm, gssnegotiate and any (The client will poll the server for what method to use).
     * 
     * - default_protocol: (string) Default protocol to use if the request's URI is missing a scheme. Either http or https.
     * 
     * @param StreamFactoryInterface $streamFactory A stream factory
     * @param RequestFactoryInterface $requestFactory An HTTP request message factory
     * @param ResponseFactoryInterface $responseFactory An HTTP response message factory
     * @param array $configuration Associative array of client configuration settings values.
     * 
     * @throws \InvalidArgumentException For an improperly structured configuration
     * @throws \InvalidArgumentException For invalid configuration settings
     */
    public function __construct(
        StreamFactoryInterface $streamFactory,
        RequestFactoryInterface $requestFactory,
        ResponseFactoryInterface $responseFactory,
        array $configuration = []
    ) {
        $this->streamFactory = $streamFactory;
        $this->requestFactory = $requestFactory;
        $this->responseFactory = $responseFactory;
        if ($configuration) {
            $this->setConfigs($configuration);
        }
    }

    public function __destruct()
    {
        if (isset($this->curlHandle)) {
            curl_close($this->curlHandle);
        }
        unset($this->curlHandle);
    }

    /**
     * Sets a client configuration setting
     * 
     * Valid configuration settings include:
     * 
     * - enable_compression: (boolean) Whether or not to gzip compress the body of every HTTP request to send.
     * 
     * - enable_decompression: (boolean) Whether or not to gzip decompress the body of every HTTP response received.
     *                         If decompression is enabled, it will only decompress if the Content-Encoding header
     *                         value of the HTTP response is 'gzip'
     * 
     * - timeout: (int) The maximum amount of time (in seconds) to wait after which the request times out.
     * 
     * - connect_timeout: (int) The maximum amount of time (in seconds) to wait while trying to connect
     *                          to the host. Use 0 to wait indefinitely.
     * 
     * - max_redirects: (int) The maximum amount of HTTP redirections to follow.
     * 
     * - http_auth: (string) The HTTP authentication method to use. Possible values for these include:
     *                       basic, digest, ntlm, gssnegotiate and any (The client will poll the server for what method to use)
     * 
     * - default_protocol: (string) Default protocol to use if the request's URI is missing a scheme. Either http or https
     *
     * @param string $name Configuration setting name
     * @param mixed $value Configration setting value
     * 
     * @return static
     * 
     * @throws \InvalidArgumentException For invalid configuration settings
     */
    public function setConfig(string $name, $value)
    {
        if (!$name or empty($value)) {
            throw new \InvalidArgumentException('Some argument(s) is/are invalid');
        }
        if (!in_array($name, array_keys(self::CONFIGS), true)) {
            throw new \InvalidArgumentException(sprintf("%s is an invalid client configuration", $name));
        }
        $value_type = gettype($value);
        if (!in_array($value_type, self::CONFIGS)) {
            throw new \InvalidArgumentException(sprintf("Incorrect value type for the setting : %s", $name));
        }
        // If the configuration setting is one having multiple possible values
        switch (true) {
            case ($name == 'default_protocol' && !preg_match('/https?/i', $value)): // fall-through
            case ($name == 'http_atuth' && !preg_match('/basic|digest|ntlm|gssnegotiate|any/i', $value)):
                throw new \InvalidArgumentException(sprintf("Invalid value for the setting: %s", $name));
                break;
        }
        $this->configs[$name] = $value;
        return $this;
    }

    /**
     * Sets the client configuration
     *
     * @param array $configuration Associative array of configuration settings values. Each setting value is indexed by its name
     * 
     * @throws \InvalidArgumentException For an improperly structured configuration
     * @throws \InvalidArgumentException For invalid configuration settings
     */
    public function setConfigs(array $configuration)
    {
        if (!$configuration) {
            throw new \InvalidArgumentException('Invalid configuration');
        }
        foreach ($configuration as $setting => $value) {
            $this->setConfig($setting, $value);
        }
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        // Pull data from the request
        $uri = $request->getUri();
        $method = strtoupper($request->getMethod());
        $requestTarget = $request->getRequestTarget();
        $version = $request->getProtocolVersion();
        $url = (string) $uri; // Get the effective URL

        // Initialize the transfer's options
        $options = [];
        $options += $this->applySettings();

        // Process the request
        if (!preg_match(self::VALID_METHODS, $method)) {
            throw (new RequestException("Invalid request method"))->setRequest($request);
        }
        $options[CURLOPT_CUSTOMREQUEST] = $method;
        $options[CURLOPT_REQUEST_TARGET] = $requestTarget;
        $options[CURLOPT_HTTP_VERSION] = $this->getVersion($version, $uri);
        $isBodyIncluded = true;

        // Initialize a cURL session
        $this->curlHandle = curl_init($url);

        switch (true) {
            case (preg_match('/HEAD/', $method)):
                $options[CURLOPT_NOBODY] = true;
                $isBodyIncluded = false;
                break;
            case (preg_match('/POST|PUT/', $method)):
                /**
                 * If the client is configured to compress all request body data
                 * and the request object is missing a Content-Encoding header
                 * field.
                 * 
                 * Then the request body data is gzip compressed and appropriate
                 * headers are added to the request to ensure remains internally
                 * consistent.
                 */
                $this->isOk($request);
                $body = $request->getBody();
                if ($this->configs['enable_compression'] && !$request->hasHeader('Content-Encoding')) {
                    if (!$body->isWritable()) {
                        throw new RequestException("Request body is not writable", RequestExceptionType::RUNTIME_ERROR, $request);
                    }
                    // Compress the request
                    $request = $this->compress($request);
                }
            case (preg_match('/POST/', $method)):
                $body = $request->getBody();
                $data = (string) $body;
                $options[CURLOPT_POSTFIELDS] = $data;
                break;
            case (preg_match('/PUT/', $method)):
                $this->isOk($request);
                $body = $request->getBody();
                $size = $body->getSize();
                $uri = $body->getMetadata('uri');
                // If the body is a PHP I/O stream
                if (preg_match('/php:\/{2}\w+/', $uri)) {
                    $file = tmpfile();
                    $contents = (string) $body;
                    fwrite($file, $contents);
                    rewind($file);
                } else {
                    $file = $body->detach();
                    if (is_null($file)) {
                        throw new RequestException("Request body is missing", RequestExceptionType::RUNTIME_ERROR, $request);
                    }
                }
                $options[CURLOPT_PUT] = true;
                $options[CURLOPT_INFILE] = $file;
                $options[CURLOPT_INFILESIZE] = $size;
                break;
        }
        $httpHeaders = $this->getHeaderFields($request->getHeaders());
        if ($httpHeaders) {
            $options[CURLOPT_HTTPHEADER] = $httpHeaders;
        }
        $options += self::DEFAULT_OPTIONS;
        curl_setopt_array($this->curlHandle, $options);
        // Send the request
        $result = curl_exec($this->curlHandle);
        // Retrieve the metadata of the transfer
        $info = curl_getinfo($this->curlHandle);
        // Check if an error occurred
        $errorNo = curl_errno($this->curlHandle);
        if ($errorNo > 0) {
            $error = curl_error($this->curlHandle);
            $body = $request->getBody();
            switch ($errorNo) {
                case CURLE_COULDNT_RESOLVE_HOST:
                    $type = NetworkExceptionType::HOST_NOT_FOUND;
                    break;
                case CURLE_OPERATION_TIMEDOUT:
                    $request = $this->getSentRequest($info, $method, $uri)->withBody($body);
                    $type = NetworkExceptionType::TIME_OUT;
                    break;
                case CURLE_UNSUPPORTED_PROTOCOL:
                    $error = "Unsupported protocol";
                    $type = NetworkExceptionType::NETWORK_ERROR;
                    break;
                case CURLE_URL_MALFORMAT:
                    $error = "The URL was not properly formatted.";
                    $type = NetworkExceptionType::NETWORK_ERROR;
                    break;
                case CURLE_COULDNT_CONNECT:
                case CURLE_COULDNT_RESOLVE_HOST:
                case CURLE_COULDNT_RESOLVE_PROXY:
                    $type = NetworkExceptionType::NETWORK_ERROR;
                    break;
                default:
                    $request = $this->getSentRequest($info, $method, $uri)->withBody($body);
                    $type = NetworkExceptionType::NETWORK_ERROR;
                    break;
            }
            throw new NetworkException($error, $type, $request);
        }
        $code = $info['http_code'];
        $reasonPhrase = $this->getReasonPhrase($result, $code);
        $response_headers_size = $info['header_size'];
        $response_headers = $this->getResponseHeaders($result, $response_headers_size);
        $response = $this->responseFactory->createResponse($code, $reasonPhrase);
        $this->setHeaders($response, $response_headers);
        if (!$isBodyIncluded) {
            return $response;
        }
        $stream = $this->streamFactory->createStream($result);
        $response = $response->withBody($stream);
        if ($this->configs['enable_decompression'] && ($response->getHeaderLine('Content-Encoding') === 'gzip')) {
            $response = $this->decompress($response);
        }
        return $response;
    }

    /**
     * Appends headers to a PSR-7 HTTP message
     *
     * @param MessageInterface $message
     * @param array $headers
     */
    private function setHeaders(MessageInterface &$message, array $headers)
    {
        foreach ($headers as $name => $values) {
            $message = $message->withHeader($name, $values);
        }
    }

    /**
     * @throws RequestException
     */
    private function isOk(RequestInterface $request)
    {
        $body = $request->getBody();
        if (!$request->hasHeader('Content-Type')) {
            throw (new RequestException("Content-Type header is missing"))->setRequest($request);
        }
        if (!$request->hasHeader('Content-Length')) {
            throw (new RequestException("Content-Length header is missing"))->setRequest($request);
        }
        $size = $body->getSize();
        if (!$size) {
            throw (new RequestException("Request body size is undetermined"))->setRequest($request);
        }
        $contentLength = (int) $request->getHeaderLine('Content-Length');
        if ($size !== $contentLength) {
            throw (new RequestException("Size of the stream does not match the 'Content-Length' header value"))->setRequest($request);
        }
        if (!$body->isReadable()) {
            throw new RequestException("Request body is not readable", RequestExceptionType::RUNTIME_ERROR, $request);
        }
    }

    /**
     * Applies the configuration settings
     * 
     * Translates the configuration settings into request options for a transfer session
     *
     * @return array Request options for a session
     */
    private function applySettings()
    {
        $options = [];
        foreach ($this->configs as $key => $value) {
            switch ($key) {
                case 'timeout':
                    $options[CURLOPT_TIMEOUT] = $value;
                    break;
                case 'connect_timeout':
                    $options[CURLOPT_CONNECTTIMEOUT] = $value;
                    break;
                case 'max_redirects':
                    $options[CURLOPT_MAXREDIRS] = $value;
                    break;
                case 'http_auth':
                    switch ($value) {
                        case 'basic':
                            $value = CURLAUTH_BASIC;
                            break;
                        case 'digest':
                            $value = CURLAUTH_DIGEST;
                            break;
                        case 'ntlm':
                            $value = CURLAUTH_NTLM;
                            break;
                        case 'gssnegotiate':
                            $value = CURLAUTH_GSSNEGOTIATE;
                            break;
                        case 'any':
                            $value = CURLAUTH_ANY;
                            break;
                    }
                    $options[CURLOPT_HTTPAUTH] = $value;
                    break;
                case 'default_protocol':
                    $options[CURLOPT_DEFAULT_PROTOCOL] = $value;
                    break;
            }
        }
        return $options;
    }

    /**
     * Retrieves all message header fields
     * 
     * @param string[][] $headers Message header values as returned by a call to `getHeaders` of a PSR-7 HTTP message
     * 
     * @return string[]
     */
    private function getHeaderFields(array $headers)
    {
        $httpHeaders = [];
        foreach ($headers as $key => $value) {
            $value = implode(",", $value);
            $header = $key . ": " . $value;
            $httpHeaders[] = $header;
        }
        return $httpHeaders;
    }

    /**
     * Retrieves the request that was sent
     *
     * @return RequestInterface
     */
    private function getSentRequest(array $info, string $method, UriInterface|string $uri)
    {
        $request_headers = $this->extractHeaders($info['request_header']);
        $request = $this->requestFactory->createRequest($method, $uri);
        $this->setHeaders($request, $request_headers);
        return $request;
    }

    /**
     * Retrieves the reason phrase from the status-line of the response
     * 
     * @param string $result The raw result of the transfer
     * @param int $code The HTTP status code of the response
     * 
     * @return string
     */
    private function getReasonPhrase(string &$result, int $code)
    {
        $lines = preg_split("/\r\n/", $result);
        $status_lines = array_filter($lines, function ($value) {
            return (bool) preg_match('/HTTP\/\d\.\d \d{3} \w+/', $value);
        });
        $statusLine = '';
        foreach ($status_lines as $status_line) {
            if (preg_match('/' . $code . '/', $status_line)) {
                $statusLine = $status_line;
                break;
            }
        }
        $reasonPhrase = substr($statusLine, 13);
        return $reasonPhrase;
    }

    /**
     * Retrieves all the response header values
     * 
     * @param string $result The raw result of the transfer
     * 
     * @return string[][]
     */
    private function getResponseHeaders(string &$result, int $headers_size)
    {
        $headers = substr($result, 0, $headers_size);
        $headers = $this->extractHeaders($headers);
        $result = substr($result, $headers_size);
        return $headers;
    }

    /**
     * Retrieves all of the header values of a request
     * 
     * @param string $request_headers The request header fields
     * 
     * @return string[][]
     */
    private function extractHeaders(string $request_headers)
    {
        $headers = [];
        $lines = preg_split("/\r\n/", $request_headers);
        foreach ($lines as $line) {
            $line = rtrim($line);
            if (preg_match('/(\S+): (.*)/', $line, $matches)) {
                $headers[$matches[1]] = (preg_match('/((.+),|;)+/', $matches[2]) && !preg_match('/Date|Expires/i', $matches[1])) ? preg_split('/,|;/', $matches[2]) : preg_split('/\n/', $matches[2]);
            }
        }
        return $headers;
    }

    /**
     * Determines which HTTP protocol version to use when sending the request
     *
     * @param string $version HTTP protocol version number from the request object
     * @param UriInterface $uri The URI of the request
     * 
     * @return int One of CURL_HTTP_VERSION_XXX constants
     */
    private function getVersion(string $version, UriInterface $uri)
    {
        $scheme = $uri->getScheme();
        switch (true) {
            case (preg_match('/1(\.0)?^$/', $version)):
                $value = CURL_HTTP_VERSION_1_0;
                break;
            case (preg_match('/^1(\.1)?$/', $version)):
                $value = CURL_HTTP_VERSION_1_1;
                break;
            case (preg_match('/^2(\.0)?$/', $version)):
                $value = CURL_HTTP_VERSION_2_0;
                break;
            case (preg_match('/^2(\.0)?$/', $version) && preg_match('/^https$/i', $scheme)):
                $value = CURL_HTTP_VERSION_2TLS;
                break;
            default:
                $value = CURL_HTTP_VERSION_NONE;
                break;
        }
        return $value;
    }

    /**
     * Compresses a HTTP message's body
     * 
     * Performs a gzip encoding and then modifies the message's body to ensure
     * it remains internally consistent
     *
     * @param MessageInterface $message
     * 
     * @return MessageInterface
     */
    private function compress(MessageInterface $message)
    {
        // Retrieve and gzip compress the contents of the message's body
        $contents = (string) $message->getBody();
        $encoded_contents = gzencode($contents, 9);
        // Empty the message's body to replace with its content with the compressed version
        $tmp_file = tmpfile();
        fwrite($tmp_file, $encoded_contents);
        $encoded_body = $this->streamFactory->createStreamFromResource($tmp_file);
        $message = $message->withBody($encoded_body);
        // Modify the headers appropriately and return
        $size = $message->getBody()->getSize();
        return $message->withHeader('Content-Encoding', 'gzip')->withHeader('Content-Length', "$size");
    }

    /**
     * Decompresses a HTTP message's body
     * 
     * Performs a gzip decoding and then modifies the message's body to ensure
     * it remains internally consistent
     *
     * @param MessageInterface $message
     * 
     * @return MessageInterface
     */
    private function decompress(MessageInterface $message)
    {
        // Retrieve and gzip decompress the contents of the message's body
        $contents = (string) $message->getBody();
        $decoded_contents = gzdecode($contents);
        // Empty the message's body to replace with its content with the decompressed version
        $stream = $message->getBody()->detach();
        ftruncate($stream, 0);
        fwrite($stream, $decoded_contents);
        $encoded_body = $this->streamFactory->createStreamFromResource($stream);
        $message = $message->withBody($encoded_body);
        // Modify the headers appropriately and return
        $size = $message->getBody()->getSize();
        return $message->withoutHeader('Content-Encoding')->withHeader('Content-Length', "$size");
    }
}

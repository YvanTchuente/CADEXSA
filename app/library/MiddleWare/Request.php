<?php

declare(strict_types=1);

namespace Application\MiddleWare;

use Psr\Http\Message\{
    UriInterface,
    StreamInterface,
    RequestInterface
};

class Request extends Message implements RequestInterface
{
    /**
     * Valid HTTP methods
     * 
     * @var string
     */
    protected const VALID_METHODS = '/GET|HEAD|POST|PUT|DELETE|TRACE|CONNECT|OPTIONS/i';

    /**
     * URI
     * 
     * @var UriInterface|null
     */
    protected $uri;

    /**
     * Request target
     * 
     * @var string
     */
    protected $target;

    /**
     * HTTP method
     * 
     * @var string
     */
    protected $method;

    /**
     * @param string $method Method of the request
     * @param UriInterface|string $uri URI of the request
     * @param string[][] $headers Header values of the request
     * @param string $version HTTP protocol version
     * @param StreamInterface $body Body of the request
     * 
     * @throws \InvalidArgumentException For any invalid argument
     **/
    public function __construct(
        string $method = 'GET',
        UriInterface|string $uri = '',
        array $headers = [],
        string $version = "HTTP/1.1",
        StreamInterface $body = new Stream('php://input')
    ) {
        $this->method = $this->checkMethod($method);
        $this->uri = $this->checkUri($uri);
        $this->target = $this->retrieveRequestTarget();
        $this->headers = $this->checkHeaders($headers);
        $this->version = $this->checkVersion($version);
        $this->body = $body;
        if (isset($this->uri) && $this->uri->getHost()) {
            $host = $this->uri->getHost();
            $this->headers['Host'] = preg_split('/\n/', $host);
        }
    }

    public function getRequestTarget()
    {
        return $this->target;
    }

    public function withRequestTarget($requestTarget)
    {
        $new_instance = clone $this;
        $new_instance->target = $requestTarget;
        return $new_instance;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function withMethod($method)
    {
        $method = $this->checkMethod($method);
        $new_instance = clone $this;
        $new_instance->method = $method;
        return $new_instance;
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        $newHeaders = $this->headers;
        if ($preserveHost) {
            $found = $this->findHeader('Host');
            $host = $uri->getHost();
            if (!$found && !empty($host)) {
                $newHeaders['Host'] = $uri->getHost();
            }
        }
        $new_instance = clone $this;
        $new_instance->uri = $uri;
        return $new_instance;
    }

    /**
     * @throws \InvalidArgumentException For invalid HTTP methods
     */
    protected function checkMethod(string $method)
    {
        if (!preg_match(self::VALID_METHODS, $method)) {
            throw new \InvalidArgumentException("Invalid HTTP method");
        }
        return strtoupper($method);
    }

    protected function checkUri($uri)
    {
        if (isset($uri) && is_string($uri)) {
            $uri = new Uri($uri);
        }
        return $uri;
    }

    protected function retrieveRequestTarget()
    {
        if (!isset($this->uri)) {
            return '/';
        }
        if ($this->method == 'CONNECT' && !empty($this->uri->getAuthority())) {
            return $this->uri->getAuthority();
        }
        $target = (!empty($this->uri->getPath())) ? $this->uri->getPath() : '/';
        if (preg_match('/GET/', $this->method)) {
            $target .= ($this->uri->getQuery()) ? '?' . $this->uri->getQuery() : '';
        }
        return $target;
    }
}

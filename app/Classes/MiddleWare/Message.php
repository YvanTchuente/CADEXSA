<?php

declare(strict_types=1);

namespace Classes\MiddleWare;

use Psr\Http\Message\{
    StreamInterface,
    MessageInterface,
};

abstract class Message implements MessageInterface
{
    /**
     * Message body
     * 
     * @var StreamInterface|null
     */
    protected $body;

    /**
     * HTTP protocol version
     * 
     * @var string
     */
    protected $version;

    /**
     * Header values
     * 
     * @var string[][]
     */
    protected $headers = [];

    public function getProtocolVersion()
    {
        return $this->version;
    }

    public function withProtocolVersion($version)
    {
        $version = $this->checkVersion($version);
        $new_instance = clone $this;
        $new_instance->version = $version;
        return $new_instance;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function hasHeader($name)
    {
        return (bool) $this->findHeader($name);
    }

    public function getHeader($name)
    {
        $header_values = [];
        $name = $this->findHeader($name);
        if ($name) {
            $header_values = $this->getHeaders()[$name];
        }
        return $header_values;
    }

    public function getHeaderLine($name)
    {
        $headerLine = '';
        $name = $this->findHeader($name);
        if ($name) {
            $headerLine = implode(",", $this->getHeader($name));
        }
        return $headerLine;
    }

    public function withHeader($name, $value)
    {
        $this->checkHeaderArgumentsValid($name, $value);
        $new_headers = $this->headers;
        if (is_array($value)) {
            $new_headers[$name] = $value;
        } else if (preg_match('/((.+),|;)+/', $value) && !preg_match('/Date|Expires/i', $name)) {
            $new_headers[$name] = preg_split('/,|;/', $value);
        } else {
            $new_headers[$name] = preg_split('/\n/', $value);
        }
        $new_instance = clone $this;
        $new_instance->headers = $new_headers;
        return $new_instance;
    }

    public function withAddedHeader($name, $value)
    {
        $this->checkHeaderArgumentsValid($name, $value);
        $new_headers = $this->headers;
        if (is_array($value)) {
            array_push($new_headers[$name], $value);
        } else {
            $new_headers[$name] .= $value;
        }
        $new_instance = clone $this;
        $new_instance->headers = $new_headers;
        return $new_instance;
    }

    public function withoutHeader($name)
    {
        $new_headers = $this->headers;
        if ($this->findHeader($name)) {
            unset($new_headers[$name]);
        }
        $new_instance = clone $this;
        $new_instance->headers = $new_headers;
        return $new_instance;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function withBody(StreamInterface $body)
    {
        if (!$body->isSeekable()) {
            throw new \InvalidArgumentException('The body is invalid');
        }
        $new_instance = clone $this;
        $new_instance->body = $body;
        return $new_instance;
    }

    protected function checkVersion($version)
    {
        if (!preg_match('/\d\.\d/', $version)) {
            throw new \InvalidArgumentException("$version is missing the protocol version number");
        }
        return preg_replace('/[^0-9\.]/', '', $version);
    }

    /**
     * @throws \InvalidArgumentException For invalid headers values array structure
     */
    protected function checkHeaders(array $headers)
    {
        if (!$headers) {
            return $headers;
        }
        $headers = array_filter($headers, function ($value, $key) {
            $isValid = is_string($key) && is_array($value);
            return $isValid;
        }, ARRAY_FILTER_USE_BOTH);
        if (count($headers) === 0) {
            throw new \InvalidArgumentException("Invalid headers");
        }
        return $headers;
    }

    /**
     * Determines whether a header exist in the list of headers
     * 
     * Searches a header by its name case-insentively and returns
     * the stored name of the header if it was found or false
     * otherwise
     * 
     * @param string $name Header name
     * 
     * @return string|false
     **/
    protected function findHeader(string $name)
    {
        foreach (array_keys($this->getHeaders()) as $header) {
            $hasFound = (bool) preg_match("/$name/i", $header);
            if ($hasFound) {
                $headerName = $name;
                break;
            }
        }
        if (isset($headerName)) {
            return $headerName;
        }
        return false;
    }

    /**
     * @throws \InvalidArgumentException For invalid header names or values.
     */
    private function checkHeaderArgumentsValid($name, $value)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException("Invalid header name");
        }
        if (!is_string($value) && !is_array($value)) {
            throw new \InvalidArgumentException("Invalid header value(s)");
        }
        if (is_array($value)) {
            foreach ($value as $item) {
                if (!is_string($item)) {
                    throw new \InvalidArgumentException("Invalid header values");
                }
            }
        }
    }
}

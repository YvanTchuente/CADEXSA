<?php

declare(strict_types=1);

namespace Classes\MiddleWare;

use Psr\Http\Message\UriInterface;

class Uri implements UriInterface
{
    /**
     * Valid URI path
     * 
     * @var string
     */
    private const VALID_PATH = '/\/?(\.+\/?)?/';

    /**
     * Supported URI schemes
     * 
     * @var string
     */
    private const SUPPORTED_SCHEMES = '/https?|ssh|ftp/i';

    /**
     * Valid query string
     * 
     * @var string
     */
    private const VALID_QUERY_STRING = '/(\w+=\w?&?)+/';

    /**
     * Valid hostnames
     * 
     * @var string
     */
    private const VALID_HOSTNAMES = '/\w{5,9}|(\d{1,3}(\b|\.)){4}|\w{3}\.\w+\.\w{2}/';

    /** 
     * Components of the URI
     * 
     * @var string[]
     */
    private $components = [];

    /**
     * Query parameters
     * 
     * @var string[]
     */
    private $queryParams;

    /**
     * @param string $uri URI to parse
     * 
     * @throws \InvalidArgumentException If the URI cannot be parsed
     **/
    public function __construct(string $uri = '')
    {
        $this->components = $this->getComponents($uri);
        $this->queryParams = $this->getQueryParams($this->components);
        if (empty($this->components['port']) && !empty($this->components['scheme'])) {
            $port = $this->mapSchemeToPort($this->components['scheme']);
            if ($port) {
                $this->components['port'] = $port;
            }
        }
    }

    public function getScheme()
    {
        if (empty($this->components['scheme'])) {
            return '';
        }
        $scheme = strtolower($this->components['scheme']);
        return $scheme;
    }

    public function getAuthority()
    {
        $Authority = '';
        if ($this->getUserInfo()) {
            $Authority .= $this->getUserInfo() . '@';
        }
        $Authority .= $this->components['host'] ?? '';
        if (!empty($this->components['port'])) {
            $Authority .= ':' . $this->components['port'];
        }
        return $Authority;
    }

    public function getUserInfo()
    {
        if (empty($this->components['user'])) {
            return '';
        }
        $userInfo = $this->components['user'];
        if ($this->components['pass']) {
            $userInfo .= ":" . $this->components['pass'];
        }
        return $userInfo;
    }

    public function getHost()
    {
        if (empty($this->components['host'])) {
            return '';
        }
        $host = strtolower($this->components['host']);
        return $host;
    }

    public function getPort()
    {
        if (empty($this->components['port'])) {
            return null;
        }
        $port = (int) $this->components['port'];
        return $port;
    }

    public function getPath()
    {
        if (empty($this->components['path'])) {
            return '';
        }
        $encodedParts = array_map("rawurlencode", explode('/', $this->components['path']));
        $path = implode("/", $encodedParts);
        return $path;
    }

    public function getQuery()
    {
        if (empty($this->queryParams)) {
            return '';
        }
        $query = '';
        foreach ($this->queryParams as $key => $value) {
            $query .= rawurlencode($key);
            if (!empty($value)) {
                $query .= '=' . rawurlencode($value);
            }
            $query .= '&';
        }
        $query = substr($query, 0, -1);
        return $query;
    }

    public function getFragment()
    {
        if (empty($this->components['fragment'])) {
            return '';
        }
        $fragment = rawurlencode($this->components['fragment']);
        return $fragment;
    }

    public function withScheme($scheme)
    {
        $new_instance = clone $this;
        if (empty($scheme) and $this->getScheme()) {
            unset($new_instance->components['scheme']);
        } else {
            if (is_string($scheme) and preg_match(self::SUPPORTED_SCHEMES, $scheme)) {
                $new_instance->components['scheme'] = $scheme;
                if (empty($new_instance->components['port']) && !empty($new_instance->components['scheme'])) {
                    $port = $new_instance->mapSchemeToPort($new_instance->components['scheme']);
                    if ($port) {
                        $new_instance->components['port'] = $port;
                    }
                }
            } else {
                throw new \InvalidArgumentException('Unsupported scheme');
            }
        }
        return $new_instance;
    }

    public function withUserInfo($user, $password = null)
    {
        $new_instance = clone $this;
        if (empty($user) and $this->getUserInfo()) {
            unset($new_instance->components['user']);
        } else {
            if (!is_string($user) || !($password && is_string($password))) {
                throw new \InvalidArgumentException('User name is invalid');
            }
            $new_instance->components['user'] = $user;
            if ($password) {
                $new_instance->components['pass'] = $password;
            }
        }
        return $new_instance;
    }

    public function withHost($host)
    {
        $new_instance = clone $this;
        if (empty($host) and $this->getHost()) {
            unset($new_instance->components['host']);
        } else {
            if (is_string($host) and preg_match(self::VALID_HOSTNAMES, strtolower($host))) {
                $new_instance->components['host'] = $host;
            } else {
                throw new \InvalidArgumentException('Invalid host name');
            }
        }
        return $new_instance;
    }


    public function withPort($port = null)
    {
        $new_instance = clone $this;
        if (empty($port)) {
            unset($new_instance->components['port']);
        } else {
            $valid_ports = range(1, 10000);
            if (is_integer($port) and in_array($port, $valid_ports)) {
                $new_instance->components['port'] = $port;
            } else {
                throw new \InvalidArgumentException('Invalid port');
            }
        }
        return $new_instance;
    }

    public function withPath($path)
    {
        $new_instance = clone $this;
        if (!is_string($path) or !preg_match(self::VALID_PATH, $path)) {
            throw new \InvalidArgumentException('Invalid path');
        }
        $new_instance->components['path'] = $path;
        return $new_instance;
    }

    public function withQuery($query)
    {
        $new_instance = clone $this;
        if (empty($query) and $this->getQuery()) {
            unset($new_instance->components['query']);
        } else {
            if (is_string($query) and preg_match(self::VALID_QUERY_STRING, $query)) {
                $new_instance->components['query'] = $query;
            } else {
                throw new \InvalidArgumentException('Invalid query string');
            }
        }
        return $new_instance;
    }

    public function withFragment($fragment)
    {
        $new_instance = clone $this;
        if (empty($fragment) and $this->getFragment()) {
            unset($new_instance->components['fragment']);
        } else {
            if (!is_string($fragment)) {
                throw new \InvalidArgumentException('Invalid argument');
            }
            $new_instance->components['fragment'] = $fragment;
        }
        return $new_instance;
    }

    public function __toString()
    {
        $uri = ($this->getScheme()) ? $this->getScheme() . '://' : '';
        if ($this->getAuthority()) {
            $uri .= $this->getAuthority();
        } else {
            $uri .= ($this->getHost()) ? $this->getHost() : '';
            $uri .= ($this->getPort()) ? ':' . $this->getPort() : '';
        }
        $path = $this->getPath();
        if ($path) {
            if ($path[0] != '/') {
                $uri .= '/' . $path;
            } else {
                $uri .= $path;
            }
        }
        $uri .= ($this->getQuery()) ? '?' . $this->getQuery() : '';
        $uri .= ($this->getFragment()) ? '#' . $this->getFragment() : '';
        return $uri;
    }

    /**
     * Returns the components of the URI
     *
     * @param string $uri
     * 
     * @return array
     */
    private function getComponents(string $uri)
    {
        $components = parse_url($uri);
        if (is_null($components) or $components === false) {
            throw new \InvalidArgumentException('Invalid URI');
        }
        return $components;
    }

    /**
     * Retrieves any query parameters present in the URI
     *
     * @param string[] $components URI components
     * 
     * @return array
     */
    private function getQueryParams($components)
    {
        $queryParams = [];
        if (!empty($components['query'])) {
            foreach (explode('&', $components['query']) as $keyValuePair) {
                list($param, $value) = explode('=', $keyValuePair);
                $queryParams[$param] = $value;
            }
        }
        return $queryParams;
    }

    private function mapSchemeToPort(string $scheme)
    {
        $port = null;
        switch (true) {
            case (preg_match('/http/i', $scheme)):
                $port = 80;
                break;
            case (preg_match('/https/i', $scheme)):
                $port = 443;
                break;
            case (preg_match('/ftp/i', $scheme)):
                $port = 21;
                break;
            case (preg_match('/ssh/i', $scheme)):
                $port = 22;
                break;
        }
        return $port;
    }
}

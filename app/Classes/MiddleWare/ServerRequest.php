<?php

declare(strict_types=1);

namespace Classes\MiddleWare;

use Psr\Http\Message\{
    UriInterface,
    StreamInterface,
    UploadedFileInterface,
    ServerRequestInterface
};

class ServerRequest extends Request implements ServerRequestInterface
{
    /**
     * Server parameters
     * 
     * @var string[]
     */
    private $serverParams = [];

    /**
     * Cookies
     * 
     * @var array
     */
    private $cookies = [];

    /**
     * Query parameters of the uri
     * 
     * @var string[]
     */
    private $queryParams = [];

    /**
     * Content type of the body
     * 
     * @var string
     */
    private $contentType;

    /**
     * Deserialized body paramters
     * 
     * @var array|object|null
     */
    private $parsedBody;

    /**
     * Request attributes
     * 
     * @var array
     */
    private $attributes = [];

    /**
     * List of uploaded files
     * 
     * @var UploadedFileInterface[]
     */
    private $uploadedFiles = [];

    /**
     * @param string $method Method of the request
     * @param UriInterface|string $uri URI of the request
     * @param array $serverParams Server parameters
     * @param string[][] $headers Header values of the request
     * @param string $version HTTP protocol version
     * @param StreamInterface $body Body of the request
     * @param array $attributes Attributes of the request
     * 
     * @throws \InvalidArgumentException For any invalid argument
     **/
    public function __construct(
        string $method = 'GET',
        UriInterface|string $uri = '',
        array $serverParams = [],
        array $headers = [],
        string $version = "HTTP/1.1",
        StreamInterface $body = new Stream('php://input'),
        array $attributes = []
    ) {
        $this->method = $this->checkMethod($method);
        $this->uri = $this->checkUri($uri);
        $this->serverParams = $serverParams;
        $this->body = $body;
        $this->version = $this->checkVersion($version);
        $this->attributes = $attributes;
        $this->target = $this->retrieveRequestTarget();
        $this->headers = $this->checkHeaders($headers);
        if (isset($this->uri) && $this->uri->getHost()) {
            $host = $this->uri->getHost();
            $this->headers['Host'] = preg_split('/\n/', $host);
        }
    }

    public function getServerParams()
    {
        return $this->serverParams;
    }

    public function getCookieParams()
    {
        return $this->cookies;
    }

    public function withCookieParams(array $cookies)
    {
        $new_instance = clone $this;
        $new_instance->cookies = $cookies;
        return $new_instance;
    }

    public function getQueryParams()
    {
        return $this->queryParams;
    }

    public function withQueryParams(array $query)
    {
        $new_instance = clone $this;
        $new_instance->queryParams = $query;
        return $new_instance;
    }

    public function getUploadedFiles()
    {
        return $this->uploadedFiles;
    }

    public function withUploadedFiles(array $uploadedFiles)
    {
        $new_instance = clone $this;
        if (!count($uploadedFiles)) {
            throw new \InvalidArgumentException('No file was uploaded');
        }
        foreach ($uploadedFiles as $uploadedFile) {
            if (!($uploadedFile instanceof UploadedFileInterface)) {
                throw new \InvalidArgumentException('Must contain instances of UploadedFileInterface');
            }
        }
        $new_instance->uploadedFiles = $uploadedFiles;
        return $new_instance;
    }

    public function getParsedBody()
    {
        return $this->parsedBody;
    }

    public function withParsedBody($data)
    {
        if (isset($data) and (!(is_array($data) and !is_object($data)) or !(!is_array($data) and is_object($data)))) {
            throw new \InvalidArgumentException("Unsupported argument type");
        }
        $new_instance = clone $this;
        $new_instance->parsedBody = $data;
        return $new_instance;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function getAttribute($name, $default = null)
    {
        return $this->attributes[$name] ?? $default;
    }

    public function withAttribute($name, $value)
    {
        $new_instance = clone $this;
        $new_instance->attributes[$name] = $value;
        return $new_instance;
    }

    public function withoutAttribute($name)
    {
        $new_instance = clone $this;
        if (isset($this->attributes[$name])) {
            unset($new_instance->attributes[$name]);
        }
        return $new_instance;
    }

    private function setQueryParams()
    {
        $queryParams = [];
        $queryString = $this->getServerParams()['QUERY_STRING'];
        if (!empty($queryString)) {
            $queryPairs = explode("&", $queryString);
            foreach ($queryPairs as $key => $value) {
                $queryParams[$key] = $value;
            }
        }
        $this->queryParams = $queryParams;
    }

    private function setParsedBody()
    {
        if (($this->getContentType() == 'application/x-www-form-urlencoded' || $this->getContentType() == 'multipart/form-data') && $this->getRequestMethod() == Constants::METHOD_POST) {
            $this->parsedBody = $_POST;
        } else if (preg_match('/application\/(hal+)?json/', $this->getContentType())) {
            $this->parsedBody = json_decode((string) $this->body);
        } else if ($_REQUEST) {
            $_request = array_diff($_REQUEST, $_COOKIE);
            $this->parsedBody = $_request;
        } else {
            $this->parsedBody = null;
        }
    }

    private function setUploadedFiles()
    {
        $uploadedFiles = [];
        if (isset($_FILES)) {
            foreach ($_FILES as $upload => $fileInfo) {
                extract($fileInfo);
                $uploadedFiles[$upload] = new UploadedFile($tmp_name, $size, $error, $name, $type);
            }
        }
        $this->uploadedFiles = $uploadedFiles;
    }

    protected function setHeaders()
    {
        if (function_exists('apache_request_headers')) {
            $this->headers = apache_request_headers();
        } else {
            $this->headers = $this->altApacheRequestHeaders();
        }
        foreach ($this->headers as $key => $value) {
            $values = preg_split("/,|;/", $value);
            $this->headers[$key] = $values;
        }
    }

    /**
     * @return string[][]
     **/
    private function altApacheRequestHeaders()
    {
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (stripos($key, 'HTTP_') !== false) {
                $headerKey = str_ireplace('HTTP_', '', $key);
                $headers[$this->explodeHeader($headerKey)] = $value;
            } else if (stripos($key, 'CONTENT_') !== false) {
                $headers[$this->explodeHeader($key)] = $value;
            }
        }
        return $headers;
    }

    private function explodeHeader($header)
    {
        $headerParts = explode('_', $header);
        $headerKey = ucwords(strtolower(implode(' ', $headerParts)));
        return str_replace(' ', '-', $headerKey);
    }

    private function getContentType()
    {
        if (!$this->contentType) {
            $contentType = $this->getHeaderLine('Content-Type');
            if (!empty($contentType)) {
                $this->contentType = $this->getHeaderLine('Content-Type');
            } else {
                $this->contentType = $this->getServerParams()['CONTENT_TYPE'] ?? 'application/octet-stream';
                $this->contentType = strtolower($this->contentType);
            }
        }
        return $this->contentType;
    }

    private function getRequestMethod()
    {
        $method = $this->getServerParams()['REQUEST_METHOD'] ?? 'GET';
        $this->method = strtoupper($method);
        return $this->method;
    }

    /**
     * Initializes the incoming request with data from global variables
     *
     * @return static
     */
    public function initialize()
    {
        $this->serverParams = $_SERVER;
        $this->setHeaders();
        $this->setQueryParams();
        $this->getRequestMethod();
        $this->setParsedBody();
        $this->setUploadedFiles();
        $this->cookies = $_COOKIE;
        $uri = $this->serverParams['REQUEST_SCHEME'] . "://" . $this->serverParams['HTTP_HOST'] . $this->serverParams['REQUEST_URI'];
        $this->uri = new Uri($uri);
        $this->target = $this->retrieveRequestTarget();
        return $this;
    }
}

<?php

declare(strict_types=1);

namespace Application\MiddleWare;

use Psr\Http\Message\{
    StreamInterface,
    ResponseInterface
};

class Response extends Message implements ResponseInterface
{
    /**
     * Recommended response reason phrases
     * 
     * @var string[]
     */
    const RECOMMENDED_REASON_PHRASES = [
        100 => "Continue",
        101 => "Switching Protocols",
        200 => "OK",
        201 => "Created",
        202 => "Accepted",
        203 => "Non-Authoritative Information",
        204 => "No Content",
        205 => "Reset Content",
        300 => "Multiple Choices",
        301 =>  "Moved Permanently",
        302 => "Found",
        303 => "See Other",
        305 => "Use Proxy",
        306 => "Unused",
        307 => "Temporary Redirect",
        400 => "Bad Request",
        401 => "Unauthorized",
        403 => "Forbidden",
        404 => "Not Found",
        405 => "Method Not Allowed",
        406 => "Not Acceptable",
        408 => "Request Timeout",
        409 => "Conflict",
        410 => "Gone",
        411 => "Length Required",
        413 => "Payload Too Large",
        414 => "URI Too Long",
        415 => "Unsupported Media Type",
        417 => "Expectation failed",
        418 => "I'm a Teapot",
        426 => "Upgrade Required",
        429 => "Too Many Requests",
        500 => "Internal Server Error",
        501 => "Not Implemented",
        502 => "Bad Gateway",
        503 => "Service Unavailable",
        504 => "Gateway Timeout",
        505 => "HTTP Version Not Supported"
    ];
    /**
     * Status code
     * 
     * @var int
     */
    private $code;

    /**
     * Reason phrase
     * 
     * @var string
     */
    private $reasonPhrase;

    /**
     * @param int $code Status code
     * @param string $reasonPhrase Reason phrase associated with the response
     * @param StreamInterface $body Body of the response
     * @param string[][] $headers Header values of the response
     * 
     * @throws \InvalidArgumentException For any invalid argument
     **/
    public function __construct(
        int $code = 200,
        string $reasonPhrase = '',
        array $headers = [],
        string $version = "HTTP/1.1",
        StreamInterface $body = new Stream('php://input')
    ) {
        $this->code = $code;
        $this->reasonPhrase = (!$reasonPhrase) ? $this->getReasonPhraseFromCode($code) : $reasonPhrase;
        $this->headers = $headers;
        $this->version = $this->checkVersion($version);
        $this->body = $body;
    }

    public function getStatusCode()
    {
        return $this->code;
    }

    public function withStatus($code, $reasonPhrase = '')
    {
        if ($code > 599) {
            throw new \InvalidArgumentException('Invalid status code argument');
        }
        $new_instance = clone $this;
        $new_instance->code = $code;
        $new_instance->reasonPhrase = (!$reasonPhrase) ? $this->getReasonPhraseFromCode($code) : $reasonPhrase;
        return $new_instance;
    }
    public function getReasonPhrase()
    {
        return $this->reasonPhrase;
    }

    /**
     * Returns the corresponding recommended reason phrase for a status code
     * if any exists
     *
     * @param integer $code Status code
     * 
     * @return string
     */
    private function getReasonPhraseFromCode(int $code)
    {
        $reason = '';
        if (in_array($code, array_keys(self::RECOMMENDED_REASON_PHRASES))) {
            $reason = self::RECOMMENDED_REASON_PHRASES[$code];
        }
        return $reason;
    }
}

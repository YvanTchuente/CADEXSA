<?php

/**
 * HTTP factory class
 * 
 * Implements all of the interfaces specified in PSR-17.
 * 
 * @author Yvan Tchuente <yvantchuente@gmail.com>
 * @copyright 2022 Yvan Tchuente
 */

declare(strict_types=1);

namespace Application\MiddleWare;

use Psr\Http\Message\{
    UriInterface,
    StreamInterface,
    RequestInterface,
    ResponseInterface,
    UriFactoryInterface,
    UploadedFileInterface,
    StreamFactoryInterface,
    ServerRequestInterface,
    RequestFactoryInterface,
    ResponseFactoryInterface,
    UploadedFileFactoryInterface,
    ServerRequestFactoryInterface,
};

/**
 * HTTP factory
 * 
 * Implements all of the interfaces specified in PSR-17.
 * 
 * @author Yvan Tchuente <yvantchuente@gmail.com>
 */
class Factory implements
    UriFactoryInterface,
    StreamFactoryInterface,
    RequestFactoryInterface,
    ResponseFactoryInterface,
    UploadedFileFactoryInterface,
    ServerRequestFactoryInterface
{
    public static function instance()
    {
        return new static;
    }

    public function createRequest(string $method, $uri): RequestInterface
    {
        if (empty($method)) {
            throw new \InvalidArgumentException("Invalid method");
        }
        if (!is_string($uri) && !($uri instanceof UriInterface)) {
            throw new \InvalidArgumentException("Invalid uri");
        }
        $request = new Request($method, $uri);
        return $request;
    }

    public function createServerRequest(string $method, $uri, array $serverParams = []): ServerRequestInterface
    {
        if (empty($method)) {
            throw new \InvalidArgumentException("Invalid method");
        }
        if (!is_string($uri) && !($uri instanceof UriInterface)) {
            throw new \InvalidArgumentException("Invalid uri");
        }
        $serverRequest = new ServerRequest($method, $uri, $serverParams);
        return $serverRequest;
    }

    /**
     * Create a new server request initialized with data from global variables
     *
     * @return ServerRequestInterface
     */
    public static function createServerRequestFromGlobals(): ServerRequestInterface
    {
        $serverRequest = (new ServerRequest())->initialize();
        return $serverRequest;
    }

    public function createStream(string $content = ''): StreamInterface
    {
        $stream = new Stream($content, ['isText' => true]);
        return $stream;
    }

    public function createStreamFromResource($resource): StreamInterface
    {
        if (!is_resource($resource)) {
            throw new \InvalidArgumentException("Invalid resource argument");
        }
        $stream = new Stream($resource);
        return $stream;
    }

    public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
    {
        if (empty($filename)) {
            throw new \InvalidArgumentException("Invalid filename");
        }
        $stream = new Stream($filename, ['mode' => $mode]);
        return $stream;
    }

    public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        $response = new Response($code, $reasonPhrase);
        return $response;
    }

    public function createUploadedFile(
        StreamInterface $stream,
        int $size = null,
        int $error = \UPLOAD_ERR_OK,
        string $clientFilename = null,
        string $clientMediaType = null
    ): UploadedFileInterface {
        $uploadedFile = new UploadedFile($stream, $size, $error, $clientFilename, $clientMediaType);
        return $uploadedFile;
    }

    public function createUri(string $uri = ''): UriInterface
    {
        $uri = new Uri($uri);
        return $uri;
    }
}

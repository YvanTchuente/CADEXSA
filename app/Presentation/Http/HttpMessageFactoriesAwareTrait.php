<?php

namespace Cadexsa\Presentation\Http;

use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;

trait HttpMessageFactoriesAwareTrait
{
    /**
     * The HTTP response factory instance.
     *
     * @var ResponseFactoryInterface|null
     */
    protected ?ResponseFactoryInterface $responseFactory = null;

    /**
     * The HTTP stream factory instance.
     *
     * @var StreamFactoryInterface|null
     */
    protected ?StreamFactoryInterface $streamFactory = null;

    /**
     * The HTTP request factory instance.
     *
     * @var RequestFactoryInterface|null
     */
    protected ?RequestFactoryInterface $requestFactory = null;

    /**
     * The HTTP server request factory instance.
     *
     * @var ServerRequestFactoryInterface|null
     */
    protected ?ServerRequestFactoryInterface $serverRequestFactory = null;

    /**
     * The URI factory instance.
     *
     * @var UriFactoryInterface|null
     */
    protected ?UriFactoryInterface $uriFactory = null;

    /**
     * The HTTP uploaded file factory instance.
     *
     * @var UploadedFileFactoryInterface|null
     */
    protected ?UploadedFileFactoryInterface $uploadedFileFactory = null;

    public function setResponseFactory(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;

        return $this;
    }

    public function setStreamFactory(StreamFactoryInterface $streamFactory)
    {
        $this->streamFactory = $streamFactory;

        return $this;
    }

    public function setRequestFactory(RequestFactoryInterface $requestFactory)
    {
        $this->requestFactory = $requestFactory;

        return $this;
    }

    public function setUriFactory(UriFactoryInterface $uriFactory)
    {
        $this->uriFactory = $uriFactory;

        return $this;
    }

    public function setUploadedFileFactory(UploadedFileFactoryInterface $uploadedFileFactory)
    {
        $this->uploadedFileFactory = $uploadedFileFactory;

        return $this;
    }

    public function setServerRequestFactory(ServerRequestFactoryInterface $serverRequestFactory)
    {
        $this->serverRequestFactory = $serverRequestFactory;

        return $this;
    }
}

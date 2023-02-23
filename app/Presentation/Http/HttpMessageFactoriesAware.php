<?php

namespace Cadexsa\Presentation\Http;

use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;

interface HttpMessageFactoriesAware
{
    public function setResponseFactory(ResponseFactoryInterface $responseFactory);

    public function setStreamFactory(StreamFactoryInterface $streamFactory);

    public function setRequestFactory(RequestFactoryInterface $requestFactory);

    public function setServerRequestFactory(ServerRequestFactoryInterface $serverRequestFactory);

    public function setUriFactory(UriFactoryInterface $uriFactory);

    public function setUploadedFileFactory(UploadedFileFactoryInterface $uploadedFileFactory);
}

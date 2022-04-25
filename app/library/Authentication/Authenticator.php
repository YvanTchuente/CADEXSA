<?php

declare(strict_types=1);

namespace Application\Authentication;

use Psr\Http\Message\{
    RequestInterface,
    ResponseInterface
};

interface Authenticator
{
    public function Authenticate(RequestInterface $request): ResponseInterface;
}

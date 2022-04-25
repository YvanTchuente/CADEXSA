<?php

declare(strict_types=1);

namespace Application\Authentication;

use Psr\Http\Message\{
    RequestInterface,
    ResponseInterface
};
use Application\Security\Decrypter;

/**
 * Represents a instance that authenticates a requester of service whose access is restricted by authentication
 */
interface Authenticator
{
    /**
     * Authenticates for a service.
     * 
     * Authenticates for a service whose access requires authentication
     * 
     * @param RequestInterface $request The client-sent HTTP request
     * @param Decrypter $decrypter [optional] Encrypted data decryption utility
     * @return ResponseInterface Response to the authentication request
     * @throws \RuntimeException If during the authentication process, some data needs to be decrypted
     **/
    public function Authenticate(RequestInterface $request, Decrypter $decrypter = null): ResponseInterface;
}

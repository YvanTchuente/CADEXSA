<?php

declare(strict_types=1);

namespace Application\Security;

/**
 * Represents an instance that decrypts data
 **/
interface Decrypter
{
    /**
     * Decrypts a cipher text
     * 
     * After decrypting using the key and an optional Initialization vector,
     * returns the decrypted data 
     * 
     * @param string $cipher The cipher text
     * @param string $key Base64-encoded key
     * @param string $iv [optional] Base64-encoded Initialization Vector
     * 
     * @return string
     * 
     * @throws \InvalidArgumentException For non-base64-encoded keys
     * @throws \RuntimeException
     **/
    public function decrypt(string $cipher, string $key, string $iv = '');
}

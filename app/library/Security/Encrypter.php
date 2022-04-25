<?php

declare(strict_types=1);

namespace Application\Security;

/**
 * Represents an instance that encrypts data
 **/
interface Encrypter
{
    /**
     * Encrypts data
     * 
     * After encryption, either returns an associative array of encryption data or cipher directly
     * if and only if the key was passed as argument and the use of an initialization vector
     * was enabled.
     * 
     * The items of the associative array of encryption data include:
     * 
     * | Key    | Description                                                           |
     * | :---   | :---                                                                  |
     * | cipher | The cipher text                                                            |
     * | key    | A randomly generated base64-encoded key used to encrypt the data      |
     * | iv     | A randomly generated base64-encode initialization vector used during  |
     * |        | the encryption process                                                |
     * 
     * @param string $data The data
     * @param string $key [optional] Base64-encoded key
     * @param bool $use_iv [optional] Whether or not to enable the use of an Initialization Vector
     * 
     * @return array|string
     * 
     * @throws \InvalidArgumentException For non-base64-encoded keys
     **/
    public function encrypt(string $data, string $key = null, bool $use_iv = true);
}

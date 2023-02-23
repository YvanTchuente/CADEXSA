<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure\Contracts;

interface Encrypter
{
    /**
     * Encrypts a piece of data.
     * 
     * @param string $data The piece of data
     * @param string $key The encryption key
     * 
     * @return string The ciphertext.

     * @throws \LogicException
     */
    public function encrypt(string $data, string $key);

    /**
     * Decrypts a ciphertext.
     * 
     * @param string $ciphertext The encrypted piece of data
     * @param string $key The encryption key
     * 
     * @return string The decrypted piece of data.
     * 
     * @throws \LogicException
     */
    public function decrypt(string $ciphertext, string $key);
}

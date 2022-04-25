<?php

declare(strict_types=1);

namespace Application\Security;

/**
 * Describes an instance aware of encryption and decryption
 */
interface SecurerAware
{
    /**
     * Sets an encrypter instance on the object
     *
     * @param Encrypter $encrypter
     */
    public function setEncrypter(Encrypter $encrypter);

    /**
     * Sets a decrypter instance on the object
     *
     * @param Decrypter $decrypter
     */
    public function setDecrypter(Decrypter $decrypter);
}

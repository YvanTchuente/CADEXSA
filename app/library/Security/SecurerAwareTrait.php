<?php

declare(strict_types=1);

namespace Application\Security;

trait SecurerAwareTrait
{
    /**
     * The encrypter instance
     * 
     * @var Encrypter|null
     */
    protected ?Encrypter $encrypter;

    /**
     * The decrypter instance
     * 
     * @var Decrypter|null 
     */
    protected ?Decrypter $decrypter;

    public function setEncrypter(Encrypter $encrypter)
    {
        $this->encrypter = $encrypter;
    }

    public function setDecrypter(Decrypter $decrypter)
    {
        $this->decrypter = $decrypter;
    }
}

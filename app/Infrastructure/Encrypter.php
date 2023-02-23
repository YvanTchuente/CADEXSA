<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure;

use Tym\Encryption\Encrypter as SmartEncrypter;
use Cadexsa\Infrastructure\Contracts\Encrypter as EncrypterInterface;

class Encrypter implements EncrypterInterface
{
    private SmartEncrypter $encrypter;

    public function __construct(private array $options = [])
    {
        $this->encrypter = new SmartEncrypter;
    }

    public function encrypt(string $data, string $key)
    {
        if (!SmartEncrypter::isBase64Encoded($key)) {
            $key = base64_encode($key);
        }
        return $this->encrypter->encrypt($data, $key, $this->options);
    }

    public function decrypt(string $ciphertext, string $key)
    {
        if (!SmartEncrypter::isBase64Encoded($key)) {
            $key = base64_encode($key);
        }
        return $this->encrypter->decrypt($ciphertext, $key, $this->options);
    }
}

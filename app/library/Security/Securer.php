<?php

declare(strict_types=1);

namespace Application\Security;

/**
 * An AES encryption and decryption utility class
 */
class Securer implements Encrypter, Decrypter
{
    /**
     * AES algorithm to use
     * 
     * @var string
     */
    private $method;

    /**
     * Cipher algorithm IV length
     * 
     * @var int
     */
    private $iv_length;

    /**
     * @throws \DomainException If the cipher method is not an AES cipher method
     **/
    public function __construct(string $method = 'aes-256-xts')
    {
        $allowed_cipher_methods = $this->getAllowedCiphersMethods(); // AES methods
        if (!in_array($method, $allowed_cipher_methods)) {
            throw new \DomainException("Cipher method not allowed");
        }
        $this->method = $method;
        $this->iv_length = openssl_cipher_iv_length($this->method);
    }

    public function encrypt(string $data, string $key = null, bool $use_iv = true)
    {
        if ($key) {
            if (!$this->isBase64Encoded($key)) {
                throw new \InvalidArgumentException("The key is not base64-encoded");
            }
            $key = base64_decode($key);
            $include_key = false;
        } else {
            $key = random_bytes($this->iv_length);
        }
        $iv = ($use_iv) ? random_bytes($this->iv_length) : ''; // Initialization vector
        $data = $this->standardize($data);
        $cipher = openssl_encrypt($data, $this->method, $key, 0, $iv);
        $key = base64_encode($key);
        $iv = base64_encode($iv);
        $encryption = ['cipher' => $cipher, 'key' => $key, 'iv' => $iv];
        if (isset($include_key) && $include_key == false) {
            unset($encryption['key']);
        }
        if (!$use_iv) {
            unset($encryption['iv']);
        }
        if (count($encryption) == 1) {
            $encryption = implode("", $encryption);
        }
        return $encryption;
    }

    public function decrypt(string $cipher, string $key, string $iv = '')
    {
        if (!$this->isBase64Encoded($key)) {
            throw new \InvalidArgumentException("The key is not base64-encoded");
        }
        $key = base64_decode($key);
        if ($iv) {
            if (!$this->isBase64Encoded($iv)) {
                throw new \InvalidArgumentException("The Initialization Vector is not base64-encoded");
            }
            if (strlen(base64_decode($key)) !== $this->iv_length) {
                throw new \InvalidArgumentException(sprintf("IV must be %d bytes long, actual length is %d", $this->iv_length, strlen(base64_decode($key))));
            }
            $iv = base64_decode($iv);
        }
        $data = openssl_decrypt($cipher, $this->method, $key, 0, $iv);
        if ($data === false) {
            throw new \RuntimeException("Initialization Vector is needed");
        }
        return trim($data);
    }

    /**
     * Pads the data to encrypt to the value
     * of `iv_length` property
     *
     * @param string $data
     * 
     * @return string
     */
    private function standardize(string $data)
    {
        $len = strlen($data);
        if ($len < $this->iv_length) {
            $data = str_pad($data, $this->iv_length);
        }
        return $data;
    }

    /**
     * Returns the list of AES algorithms
     *
     * @return string[]
     */
    private function getAllowedCiphersMethods()
    {
        return array_filter(openssl_get_cipher_methods(), function ($item) {
            return (bool) preg_match('/aes-\d{3}-/', $item);
        });
    }

    /**
     * Checks if data is a valid base64 encoding
     *
     * @return boolean
     */
    private function isBase64Encoded(string $data)
    {
        try {
            return (isset($data)) ? (base64_encode(base64_decode($data, true)) === $data) : false;
        } catch (\Throwable $e) {
            return false;
        }
    }
}

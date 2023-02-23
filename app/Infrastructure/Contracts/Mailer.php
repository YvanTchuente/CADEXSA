<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure\Contracts;

interface Mailer
{
    /**
     * Sets the sender's address and name.
     *
     * @param string $address The email address of the sender
     * @param string $name [optional] The name of the sender
     * 
     * @return static

     * @throws \InvalidArgumentException If the address is invalid.
     */
    public function from(string $address, string $name = '');

    /**
     * Sets the recipient's address and name.
     *
     * @param string $address The email address of the recipient
     * @param string $name [optional] The name of the recipient
     * 
     * @return static
     * 
     * @throws \InvalidArgumentException If the address is invalid.
     */
    public function to(string $address, string $name = '');

    /**
     * Sends a new message.
     *
     * @param string $message The message.
     * @param string $subject [optional] The subject of the message.
     * @param bool $isHTML Sets the message type to HTML if set to true,
     *                     otherwise the message type is set to plain text
     * 
     * @throws \LogicException
     * @throws \RuntimeException if the message could not be sent.
     */
    public function send(string $message, string $subject = '', bool $isHTML = false): void;
}

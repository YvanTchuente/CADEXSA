<?php

declare(strict_types=1);

namespace Application;

/**
 * Describes a mailer
 * 
 * This interface provides a wrapper around the most common operations
 * of a mailer. 
 * 
 * None of the methods of this interface SHOULD NOT raise an exception.
 * 
 * Users of the mailer SHOULD call the getError method to retrieve 
 * the most recent error message.
 */
interface MailerInterface
{
    /**
     * Sets the sender of the email
     *
     * @param string $address Email address of the sender
     * @param string $name Name of the sender
     * 
     * @return bool false on error, otherwise true.
     *              Retrieve the error by calling getError()
     */
    public function setSender(string $address, string $name);

    /**
     * Sets the recipient of the email
     *
     * @param string $address Email address of the recipient
     * @param string $name Name of the recipient
     * 
     * @return bool false on error, otherwise true.
     *              Retrieve the error by calling getError()
     */
    public function setRecipient(string $address, string $name = '');

    /**
     * Sets multiple recipients for the email
     * 
     * Takes either an array of recipients addresses or an array of associative arrays where each array item 
     * contains the address and the name of each recipient.
     * 
     * @param array $recipients Recipients addresses and/or names
     * 
     * @throws \InvalidArgumentException If in case a two-dimensional array is given and either the inner arrays element 'address' or 'name' is missing.
     * 
     * @return bool false on error, otherwise true.
     *              Retrieve the error by calling getError()
     */
    public function setRecipients(array $recipients);

    /**
     * Adds a "Reply-To" address for the email
     *
     * @param string $address The email address to reply to
     * @param string $name Name to bind to the "Reply-To" email address
     * 
     * @return bool false on error, otherwise true.
     *              Retrieve the error by calling getError()
     */
    public function addReplyAddress(string $address, string $name = '');

    /**
     * Sets the body of the email
     *
     * @param string $body Email body message
     * @param string $subject Subject of the email
     * @param bool $isHTML Sets the body message type to HTML if set to true,
     *                     otherwise message type is set to plain text
     * 
     * @return bool false on error, otherwise true.
     *              Retrieve the error by calling getError()
     */
    public function setBody(string $body, string $subject = null, bool $isHTML = false);

    /**
     * Sends the email
     * 
     * @return bool false on error, otherwise true.
     *              Retrieve the error by calling getError()
     */
    public function send();

    /**
     * Returns the most recent error message
     * 
     * @return string
     */
    public function getError();
}

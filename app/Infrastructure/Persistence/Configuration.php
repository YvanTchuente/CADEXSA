<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure\Persistence;

/**
 * Represents a database connection configuration.
 */
class Configuration
{
    /**
     * The database driver name option.
     */
    private string $driver;

    /**
     * The database server hostname option.
     */
    private string $host;

    /**
     * The database name option.
     */
    private string $database;

    /**
     * The database username option.
     */
    private string $username;

    /**
     * The database connection password option.
     */
    private string $password = '';

    /**
     * The port number option.
     */
    private ?int $port = null;

    /**
     * @param string $driver The database driver name
     * @param string $host The database server hostname
     * @param string $database The database name
     * @param string $username The database username
     * @param string $password The connection password
     * @param integer|null $port The port number for connection
     */
    public function __construct(
        string $driver,
        string $host,
        string $database,
        string $username,
        string $password = '',
        int $port = null
    ) {
        switch (true) {
            case (!$driver):
                throw new \DomainException('Invalid database driver.');
                break;
            case (!$host):
                throw new \DomainException('Invalid host name or address.');
                break;
            case (!$database):
                throw new \DomainException('Invalid database name.');
                break;
            case (!$username):
                throw new \DomainException('Invalid username.');
                break;
            case (isset($port) && !$port):
                throw new \DomainException('Invalid port.');
                break;
        }
        $this->driver = $driver;
        $this->host = $host;
        $this->database = $database;
        $this->username = $username;
        $this->password = $password;
        $this->port = $port;
    }

    /**
     * Gets the database driver name.
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * Returns an instance with specified driver.
     *
     * @param string $driver The driver name.
     */
    public function withDriver(string $driver)
    {
        if (!$driver) {
            throw new \DomainException('Invalid database driver');
        }
        $instance = clone $this;
        $instance->driver = $driver;
        return $instance;
    }

    /**
     * Gets the database database server hostname.
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Returns an instance with the database specified host.
     *
     * @param string $host The host name.
     */
    public function withHost(string $host)
    {
        if (!$host) {
            throw new \DomainException('Invalid host name or address');
        }
        $instance = clone $this;
        $instance->host = $host;
        return $instance;
    }

    /**
     * Gets the database port number.
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Returns an instance with the database specified port number.
     *
     * @param integer $port The port number.
     */
    public function withPort(int $port)
    {
        if (!$port) {
            throw new \DomainException('Invalid port');
        }
        $instance = clone $this;
        $instance->port = $port;
        return $instance;
    }

    /**
     * Gets the database database name.
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * Returns an instance with the database specified database.
     *
     * @param string $database The database name.
     */
    public function withDatabase(string $database)
    {
        if (!$database) {
            throw new \DomainException('Invalid database name');
        }
        $instance = clone $this;
        $instance->database = $database;
        return $instance;
    }

    /**
     * Gets the database database server username.
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Returns an instance with the database specified username.
     */
    public function withUsername(string $username)
    {
        if (!$username) {
            throw new \DomainException('Invalid username');
        }
        $instance = clone $this;
        $instance->username = $username;
        return $instance;
    }

    /**
     * Gets the database connection password.
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Returns an instance with the database specified password.
     */
    public function withPassword(string $password)
    {
        if (!$password) {
            throw new \DomainException('Invalid password');
        }
        $instance = clone $this;
        $instance->password = $password;
        return $instance;
    }
}

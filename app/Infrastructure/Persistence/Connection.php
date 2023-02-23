<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure\Persistence;

/**
 * Represents a database connection.
 */
class Connection
{
    /**
     * The active PDO connection.
     * 
     * @var \PDO|null
     */
    public readonly ?\PDO $pdo;

    /**
     * The configuration settings.
     */
    public readonly Configuration $configuration;

    /**
     * The connection name.
     */
    private string $name;

    /**
     * @param Configuration $configuration The connection configuration settings.
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Opens the connection.
     * 
     * @throws \PDOException On failure.
     */
    public function open()
    {
        $driver = $this->configuration->getDriver();
        $hostname = $this->configuration->getHost();
        $database = $this->configuration->getDatabase();
        $username = $this->configuration->getUsername();
        $password = $this->configuration->getPassword();
        $port = $this->configuration->getPort();

        $dsn = $this->makeDsn($driver, $hostname, $database, $port);
        $this->pdo = new \PDO($dsn, $username, $password, [\PDO::ERRMODE_EXCEPTION]);
    }

    /**
     * Closes the connection.
     */
    public function close()
    {
        if (isset($this->pdo)) {
            $this->pdo = null;
        }
    }

    /**
     * Determines if the connection is open.
     * 
     * @return bool
     */
    public function isOpen()
    {
        return isset($this->pdo);
    }

    /**
     * Gets the connection's name.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the connection's name.
     */
    public function name(string $name)
    {
        if (!$name) {
            throw new \LengthException("Invalid name");
        }

        $this->name = $name;
        return $this;
    }

    private function makeDsn(string $driver, string $hostname, string $connectionname, int $port = null)
    {
        $dsn = "$driver:host=$hostname";

        if ($port) {
            $dsn .= ":$port";
        }

        $dsn .= ";dbname=$connectionname";
        return $dsn;
    }

    public function __clone()
    {
    }
}

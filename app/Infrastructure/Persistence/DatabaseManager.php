<?php

declare(strict_types=1);

namespace Cadexsa\Infrastructure\Persistence;

use Cadexsa\Infrastructure\Application;

class DatabaseManager
{
    /**
     * The application instance.
     */
    private Application $app;

    /**
     * The active connection instances.
     *
     * @var Connection[]
     */
    private array $connections = [];

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Retrieves a database connection instance.
     */
    public function getConnection(string $name = null): Connection
    {
        $name = $name ?? $this->getDefaultConnection();

        if (!isset($this->connections[$name])) {
            $this->connections[$name] = $this->makeConnection($name);
        }

        return $this->connections[$name];
    }

    /**
     * Return all of the created connections.
     *
     * @return Connection[]
     */
    public function getConnections()
    {
        return $this->connections;
    }

    /**
     * Disconnects from a given database.
     */
    public function disconnect(string $name = null)
    {
        if (isset($this->connections[$name = $name ?? $this->getDefaultConnection()])) {
            $this->connections[$name]->close();
        }
    }


    /**
     * Get the default connection name.
     *
     * @return string
     */
    public function getDefaultConnection()
    {
        return $this->app->getConfig()->get('database.default');
    }

    /**
     * Set the default connection name.
     */
    public function setDefaultConnection(string $name)
    {
        $this->app->getConfig()->set('database.default', $name);
    }

    /**
     * Make the database connection instance.
     */
    private function makeConnection(string $name): Connection
    {
        $config = $this->configuration($name);

        $connection = (new Connection($config))->name($name);
        $connection->open();

        return $connection;
    }

    /**
     * Get the configuration for a connection.
     *
     * @throws \InvalidArgumentException
     */
    private function configuration(string $name): Configuration
    {
        $name = $name ?? $this->getDefaultConnection();

        $config = $this->app->getconfig()->get("database.connections." . $name);
        if (is_null($config)) {
            throw new \InvalidArgumentException("Database connection [{$name}] not configured.");
        }

        return new Configuration($config['driver'], $config['host'], $config['database'], $config['username'], $config['password']);
    }
}

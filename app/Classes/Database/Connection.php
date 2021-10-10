<?php

declare(strict_types=1);

namespace Classes\Database;

/**
 * Database connection class
 */
class Connection implements Connector
{
    /** 
     * The Data Source Name
     * 
     * @var string
     */
    private $dsn;

    /** 
     * Database credentials
     * 
     * @var string[]
     */
    private $params;

    /**
     * The connection to the database server
     * 
     * @var \PDO
     */
    private $connection;

    /**
     * @param string[] $params Database credentials
     */
    public function __construct(array $params)
    {
        if (
            empty($params['driver']) or
            empty($params['host']) or
            empty($params['user']) or
            !isset($params['password']) or
            empty($params['dbname'])
        ) {
            throw new \InvalidArgumentException("Missing database credentials");
        }
        $this->dsn = $this->makeDsn($params);
        $this->params = $params;
        $this->connect();
    }

    /**
     * Constructs a Data Source Name from provided database credentials
     * 
     * Constructs and returns the DSN from the database credentials
     * 
     * @param string[] $params Database credentials
     * 
     * @return string
     */
    private function makeDsn(array &$params)
    {
        $dsn = $params['driver'] . ':';
        foreach ($params as $key => $value) {
            if (preg_match('/host|dbname/', $key)) {
                $dsn .= $key . '=' . $value . ';';
                unset($params[$key]);
            } else if (preg_match('/user|password/', $key)) {
                continue;
            } else {
                unset($params[$key]);
            }
        }
        $dsn = substr($dsn, 0, -1);
        return $dsn;
    }

    /**
     * Connects to the database
     */
    private function connect()
    {
        $this->connection = new \PDO($this->dsn, $this->params['user'], $this->params['password']);
    }

    public function getConnection(): \PDO
    {
        return $this->connection;
    }

    public function __sleep()
    {
        return array('dsn', 'params');
    }

    public function __wakeup()
    {
        $this->connect();
    }
}

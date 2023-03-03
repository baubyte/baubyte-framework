<?php

namespace Baubyte\Database\Drivers;

/**
 * Drivers Interface
 */
interface DatabaseDriver {
    /**
     * Create a connection to the database.
     *
     * @param string $protocol
     * @param string $host
     * @param integer $port
     * @param string $database
     * @param string $username
     * @param string $password

     */
    public function connect(
        string $protocol,
        string $host,
        int $port,
        string $database,
        string $username,
        string $password
    );

    /**
     * Last id insert
     *
     * @return void
     */
    public function lastInsertId();

    /**
     * Close connection.
     *
     * @return void
     */
    public function close();

    /**
     * Execute a statement and return the response.
     *
     * @param string $query
     * @param array $bind Values to be replaced in the statement.
     * @return mixed statement result.
     */
    public function statement(string $query, array $bind = []): mixed;
}

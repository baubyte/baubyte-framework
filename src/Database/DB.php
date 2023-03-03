<?php

namespace Baubyte\Database;

use Baubyte\Database\Drivers\DatabaseDriver;

/**
 * Database connection.
 */
class DB {
    /**
     * Initialize database connection.
     */
    public static function connect(array $config) {
        [
            'connection' => $protocol,
            'host' => $host,
            'port' => $port,
            'database' => $database,
            'username' => $username,
            'password' => $password,
        ] = $config;

        app(DatabaseDriver::class)->connect(
            $protocol,
            $host,
            $port,
            $database,
            $username,
            $password
        );
    }

    /**
     * Run statement and get response.
     *
     * @param string $query
     * @param array $bind
     * @return mixed Database response.
     */
    public static function statement(string $query, array $bind = []) {
        return app(DatabaseDriver::class)->statement($query, $bind);
    }

    /**
     * Close database connection.
     */
    public static function close() {
        app(DatabaseDriver::class)->close();
    }
}

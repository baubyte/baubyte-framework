<?php

namespace Baubyte\Database\Drivers;

use PDO;

/**
 * PHP PDO wrapper.
 */
class PdoDriver implements DatabaseDriver {
    /**
     * PDO Instance
     *
     * @var PDO|null
     */
    protected ?PDO $pdo;

    /**
     * {@inheritdoc}
     */
    public function connect(
        string $protocol,
        string $host,
        int $port,
        string $database,
        string $username,
        string $password
    ) {
        $dsn = "{$protocol}:host={$host};port={$port};dbname={$database}";
        $this->pdo = new PDO($dsn, $username, $password);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * {@inheritdoc}
     */
    public function close() {
        $this->pdo = null;
    }

    /**
     * {@inheritdoc}
     */
    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }

    /**
     * {@inheritdoc}
     */
    public function statement(string $query, array $bind = []): mixed {
        $statement = $this->pdo->prepare($query);
        $statement->execute($bind);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}

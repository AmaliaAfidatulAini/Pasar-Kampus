<?php

namespace Core;

use PDO;
use PDOStatement;

class Database
{
    private PDO $pdo;
    public function __construct(?array $config = null)
    {
        $config ??= require dirname(__DIR__) . '/config/database.php';

        $driver = $config['driver'] ?? 'pgsql';
        $dsn = sprintf(
            '%s:host=%s;port=%s;dbname=%s',
            $driver,
            $config['host'],
            $config['port'],
            $config['database']
        );

        if ($driver !== 'pgsql') {
            $dsn .= ';charset=' . $config['charset'];
        }

        $this->pdo = new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }

    public function connection(): PDO
    {
        return $this->pdo;
    }

    public function query(string $sql, array $bindings = []): PDOStatement
    {
        $statement = $this->pdo->prepare($sql);
        $statement->execute($bindings);

        return $statement;
    }

    public function select(string $sql, array $bindings = []): array
    {
        return $this->query($sql, $bindings)->fetchAll();
    }

    public function statement(string $sql, array $bindings = []): bool
    {
        return $this->query($sql, $bindings) !== false;
    }
}

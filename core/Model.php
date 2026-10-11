<?php


namespace Core;

use PDOStatement;

abstract class Model
{
    protected string $table_name = '';
    private Database $database;

    public function __construct()
    {
        $this->database = new Database();
    }

    protected function query(string $sql, array $bindings = []): PDOStatement
    {
        return $this->database->query($sql, $bindings);
    }
}

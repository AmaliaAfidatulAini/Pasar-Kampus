<?php

$driver = $_ENV['DB_DRIVER'] ?? getenv('DB_DRIVER') ?: 'pgsql';
$host   = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?: '127.0.0.1';
$port   = $_ENV['DB_PORT'] ?? getenv('DB_PORT') ?: '5432';
$db     = $_ENV['DB_NAME'] ?? $_ENV['DB_DATABASE'] ?? getenv('DB_NAME') ?: getenv('DB_DATABASE') ?: 'pasar_kampus';
$user   = $_ENV['DB_USER'] ?? $_ENV['DB_USERNAME'] ?? getenv('DB_USER') ?: getenv('DB_USERNAME') ?: 'postgres';
$pass   = $_ENV['DB_PASS'] ?? $_ENV['DB_PASSWORD'] ?? getenv('DB_PASS') ?: getenv('DB_PASSWORD') ?: 'postgres';

try {
    $pdo = new PDO("$driver:host=$host;port=$port;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

function fetch(PDOStatement $stmt) {
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function fetchAll(PDOStatement $stmt, bool $assoc = true) {
    return $stmt->fetchAll($assoc ? PDO::FETCH_ASSOC : PDO::FETCH_NUM);
}

function fetchColumn(PDOStatement $stmt) {
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function executeQuery(string $query, array $bind = [], ?callable $callback = null) {
    global $pdo;

    if ($callback !== null && (!is_string($callback) || !in_array($callback, ['fetch', 'fetchAll', 'fetchColumn'], true))) {
        throw new InvalidArgumentException('Callback must be fetch, fetchAll, or fetchColumn.');
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute($bind);

    if ($callback === null) {
        return $stmt;
    }

    return $callback === 'fetchAll'
        ? fetchAll($stmt, $bind === [] || is_string(array_key_first($bind)))
        : $callback($stmt);
}
<?php

function route(string $route): string
{
    return rtrim(BASEURL, '/') . '/' . ltrim($route, '/');
}

function redirect(string $route, int $response_code = 301): void
{
    http_response_code($response_code);
    header('Location: ' . route($route));
    exit;
}

function esc(string $string): string
{
    return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function include_component(string $name, ?array $data = null): void
{
    if (preg_match('/^[A-Za-z0-9_-]+(?:\.php)?$/', $name) !== 1) {
        throw new InvalidArgumentException('Invalid component name.');
    }

    $filename = str_ends_with($name, '.php') ? $name : $name . '.php';
    $path = (defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__)) . '/components/' . $filename;
    if (!is_file($path)) {
        throw new RuntimeException('Component not found: ' . $name);
    }

    extract($data ?? [], EXTR_SKIP);
    require $path;
}
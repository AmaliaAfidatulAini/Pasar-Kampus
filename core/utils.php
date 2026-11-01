<?php

function route(string $route): string
{
    return rtrim(BASEURL, '/') . '/' . ltrim($route, '/');
}

function asset(string $target_dir): string
{
    return rtrim(BASEURL, '/') . '/public/assets/' . ltrim($target_dir, '/');
}

function upload(string $target_dir): string
{
    return rtrim(BASEURL, '/') . '/public/uploads/' . ltrim($target_dir, '/');
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

function parse_errors(object $result): array
{
    static $messages = [
        'required' => ':label wajib diisi.',
        'string' => ':label harus berupa teks.',
        'email' => 'Format :label tidak valid.',
        'min' => ':label minimal :value karakter.',
        'max' => ':label maksimal :value karakter.',
    ];

    $errors = [];

    foreach ($result->getFailedRules() as $failed) {
        $field = (string) ($failed['key'] ?? '');
        $rule = (string) ($failed['rule'] ?? '');
        $message = (string) ($failed['message'] ?? 'Data tidak valid.');

        if (isset($messages[$rule])) {
            $message = str_replace(
                [':label', ':value'],
                [ucfirst($field), (string) ($failed['extraParams']['value'] ?? '')],
                $messages[$rule]
            );
        }

        $errors[$field] = $message;
    }

    return $errors;
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


class Flush
{
    public static function start(): void
    {
        ob_start();
    }

    public static function end(): string
    {
        return ob_get_clean() ?: '';
    }
}
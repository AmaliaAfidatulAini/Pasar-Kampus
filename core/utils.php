<?php

function route(string $route): string
{
    return rtrim(BASEURL, '/') . '/' . ltrim($route, '/');
}

/**
 * Path + query of the current request, without the BASEURL prefix.
 * Returns '' when the request carries no usable URI.
 */
function current_request_path(): string
{
    $uri = (string) ($_SERVER['REQUEST_URI'] ?? '');
    if ($uri === '' || $uri[0] !== '/') {
        return '';
    }

    $basePath = rtrim((string) parse_url(BASEURL, PHP_URL_PATH), '/');
    if ($basePath !== '' && ($uri === $basePath || str_starts_with($uri, $basePath . '/'))) {
        $uri = substr($uri, strlen($basePath));
    }

    return $uri === '' ? '/' : $uri;
}

/**
 * Normalizes a `?redirect=` value into a same-app path.
 * Returns '' for absolute URLs, protocol-relative URLs, backslashes, control
 * characters, and the auth pages themselves (they would bounce in a loop).
 */
function redirect_target(mixed $value): string
{
    if (!is_string($value)) {
        return '';
    }

    $value = trim($value);
    if ($value === '' || $value[0] !== '/' || str_starts_with($value, '//')) {
        return '';
    }
    if (str_contains($value, '\\') || preg_match('/[\x00-\x1F\x7F]/', $value) === 1) {
        return '';
    }

    $path = parse_url($value, PHP_URL_PATH);
    if (!is_string($path) || $path === '') {
        return '';
    }
    foreach (['/login', '/register', '/logout'] as $blocked) {
        if ($path === $blocked || str_starts_with($path, $blocked . '/')) {
            return '';
        }
    }

    return $value;
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

function response_json(array $data, int $response_code = 200): void
{
    header('Content-Type: application/json; charset=UTF-8');
    http_response_code($response_code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
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
        'array' => ':label tidak valid.',
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
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

function format_rupiah(int|float $number): string
{
    return 'Rp. ' . number_format($number, 0, ',', '.');
}

function format_tanggal(string $datetime): string
{
    $time = strtotime($datetime);
    if ($time === false) {
        return $datetime;
    }

    $bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    return (int) date('j', $time) . ' ' . $bulan[(int) date('n', $time) - 1] . ' ' . date('Y', $time);
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

/**
 * Reduces Quill-produced HTML to an attribute-free subset (p, br, strong, b,
 * em, i, u, s, list, heading, blockquote). Dropping every attribute removes
 * inline handlers, javascript: URLs, and style. Text of unknown tags survives.
 */
function sanitize_rich_text(string $html): string
{
    $allowed = '<p><br><strong><b><em><i><u><s><ul><ol><li><h1><h2><h3><h4><blockquote>';
    $tags = 'p|br|strong|b|em|i|u|s|ul|ol|li|h1|h2|h3|h4|blockquote';

    // Drop dangerous element bodies; strip_tags alone would keep their text.
    $html = preg_replace(
        '~<(script|style|iframe|object|embed|svg|math)\b[^>]*>.*?</\1>~is',
        '',
        $html
    );
    $html = strip_tags($html, $allowed);

    // Rewrite allowed tags without attributes: drops on* handlers and URLs.
    $html = preg_replace_callback(
        '~<\s*(/?)\s*(' . $tags . ')\b[^>]*>~i',
        static fn (array $m): string => '<' . ($m[1] === '/' ? '/' : '') . strtolower($m[2]) . '>',
        $html
    );

    return trim($html);
}
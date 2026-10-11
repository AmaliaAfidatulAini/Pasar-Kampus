<?php

function debug_wrapper(callable $callback, mixed ...$args): void
{
    $id = 'dbg_' . uniqid();
    ob_start();
    $callback(...$args);
    $out = htmlspecialchars(ob_get_clean(), ENT_QUOTES, 'UTF-8');

    echo '<style>.debug-pre{background:#1e1e1e;color:#d4d4d4;font-family:Consolas,monospace;font-size:13px;line-height:1.5;padding:16px;border-radius:8px;border:1px solid #333;overflow-x:auto;white-space:pre-wrap;word-wrap:break-word;margin:12px 0}.dbg-bracket{color:#fff}.dbg-key{color:#ce9178;font-weight:bold}.dbg-arrow{color:#f44747;font-weight:bold}.dbg-array{color:#4ec9b0;font-style:italic}.dbg-type{color:#f44747;font-style:italic}.dbg-num{color:#b5cea8}.dbg-str{color:#9cdcfe}.dbg-struct{color:#569cd6;font-weight:bold}</style>';
    echo '<pre id="' . $id . '" class="debug-pre">' . $out . '</pre>';
    echo '<script>(function(){var e=document.getElementById("' . $id . '");if(!e)return;var c=e.innerHTML;c=c.replace(/(\[)([^\]]+)(\])\s*(&gt;|=&gt;|=>)/g,\'<span class="dbg-bracket">[</span><span class="dbg-key">$2</span><span class="dbg-bracket">]</span> <span class="dbg-arrow">=&gt;</span>\').replace(/\barray\b(?=\s*\()/gi,\'<span class="dbg-array">array</span>\').replace(/\b(string|int|float|double|bool|object|resource|NULL|null)\b(?=\s*\()/gi,\'<span class="dbg-type">$1</span>\').replace(/(\()(\d+)(\))/g,\'$1<span class="dbg-num">$2</span>$3\').replace(/&quot;(.*?)&quot;/g,\'&quot;<span class="dbg-str">$1</span>&quot;\').replace(/\b(Array|Object)\b(?=\s*\n|\s*\()/g,\'<span class="dbg-struct">$1</span>\');e.innerHTML=c;})();</script>';
}

function d(mixed $data): void
{
    debug_wrapper("print_r", $data);
}

function dd(mixed $data): void
{
    debug_wrapper("print_r", $data);
    die;
}

function v(mixed $data): void
{
    debug_wrapper("var_dump", $data);
}
function vd(mixed $data): void
{
    debug_wrapper("var_dump", $data);
    die;
}

function redirect(string $route, int $response_code = 301): void
{
    http_response_code($response_code);
    header('Location: ' . route($route));
    exit;
}

function json(mixed $data, int $response_code = 200): void
{
    http_response_code($response_code);
    header('Content-Type: application/json');
    echo json_encode($data);
}

function view(string $path, array $data = []): void
{
    $view = BASE_PATH . '/app/views/' . trim($path, '/') . '.php';

    if (!is_file($view)) {
        throw new RuntimeException("View [{$path}] not found.");
    }

    extract($data, EXTR_SKIP);
    require $view;
}

function route(string $route): string
{
    return rtrim(BASEURL, '/') . '/' . ltrim($route, '/');
}

function asset(string $path, bool $raw = false): string
{
    $path = ltrim($path, '/');
    $file = BASE_PATH . '/public/' . $path;
    $url = route('/' . $path);

    if ($raw) {
        return $url;
    }

    return match (strtolower(pathinfo($file, PATHINFO_EXTENSION))) {
        'js' => '<script src="' . esc($url) . '"></script>',
        'css' => '<link rel="stylesheet" href="' . esc($url) . '">',
        default => $url,
    };
}

function esc(string $string): string
{
    return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function session(?object $value = null): ?object
{
    if (APP_SESSION === 'COOKIE') {
        if ($value !== null) {
            $payload = base64_encode(json_encode($value, JSON_THROW_ON_ERROR));
            $token = $payload . '.' . hash_hmac('sha256', $payload, APP_SESSION_SECRET);
            setcookie('app_session', $token, [
                'expires' => time() + 86400,
                'path' => '/',
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
            return $value;
        }

        [$payload, $signature] = array_pad(explode('.', $_COOKIE['app_session'] ?? '', 2), 2, '');
        $valid = $payload !== '' && hash_equals(hash_hmac('sha256', $payload, APP_SESSION_SECRET), $signature);
        $data = $valid ? json_decode(base64_decode($payload, true) ?: '', true) : null;
        return is_array($data) ? (object) $data : null;
    }

    if ($value !== null) {
        $_SESSION['user'] = (array) $value;
        return $value;
    }

    return isset($_SESSION['user']) ? (object) $_SESSION['user'] : null;
}

function remove_session(): void
{
    if (APP_SESSION === 'COOKIE') {
        setcookie('app_session', '', ['expires' => time() - 3600, 'path' => '/', 'httponly' => true, 'samesite' => 'Lax']);
        unset($_COOKIE['app_session']);
        return;
    }

    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}

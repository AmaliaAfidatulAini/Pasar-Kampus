<?php


if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

$env = static function (string $key, mixed $default = null): mixed {
    return $_ENV[$key] ?? $_SERVER[$key] ?? $default;
};

define('APP_ENV', $env('APP_ENV', 'local'));
define('APP_DEBUG', filter_var($env('APP_DEBUG', true), FILTER_VALIDATE_BOOL));
define('APP_URL', rtrim($env('APP_URL', 'http://localhost'), '/'));
define('APP_NAME', $env('APP_NAME', 'My PHP Project'));
define('APP_SESSION', strtoupper($env('APP_SESSION', 'PHP_SESSION')));
define('APP_SESSION_SECRET', $env('APP_SESSION_SECRET', APP_NAME));

if (!defined('BASEURL')) {
    define('BASEURL', APP_URL);
}

date_default_timezone_set($env('APP_TIMEZONE', 'UTC'));

if (APP_DEBUG) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
}

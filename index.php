<?php
declare(strict_types=1);

// Define project basepath
define('BASE_PATH', __DIR__);

// Autoload composer library
$autoloadPath = BASE_PATH . '/vendor/autoload.php';
if (is_file($autoloadPath)) {
    require_once $autoloadPath;
}

// Load ENV
if (class_exists('Dotenv\\Dotenv')) {
    $dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
    $dotenv->safeLoad();
}

// Define BASEURL
$appUrl = $_ENV['APP_URL'] ?? getenv('APP_URL');
if ($appUrl === false || $appUrl === null) {
    $appUrl = '';
}
define('BASEURL', (string) $appUrl);

// Require Core Modules
require_once BASE_PATH . '/core/debug.php';
require_once BASE_PATH . '/core/utils.php';
require_once BASE_PATH . '/core/session.php';
require_once BASE_PATH . '/core/database.php';
require_once BASE_PATH . '/core/storage.php';

// Routing Logic
$notFound = static function (): never {
    redirect('404', 404);
    exit;
};

$rawRoute = $_GET['url'] ?? '';
if (!is_string($rawRoute)) {
    $notFound();
}

$route = trim(rawurldecode($rawRoute), '/');
$segments = [];

if ($route !== '') {
    if (str_contains($route, "\\") || str_contains($route, "\0")) {
        $notFound();
    }

    $segments = explode('/', $route);
    $lastSegment = array_key_last($segments);
    foreach ($segments as $index => $segment) {
        if ($segment === '' || $segment === '.' || $segment === '..') {
            $notFound();
        }

        $isExplicitPhp = $index === $lastSegment
            && preg_match('/\\A[A-Za-z0-9_-]+\\.php\\z/', $segment) === 1;
        $isSafeSegment = preg_match('/\\A[A-Za-z0-9_-]+\\z/', $segment) === 1;

        if (!$isSafeSegment && !$isExplicitPhp) {
            $notFound();
        }
    }
}

$pagesPath = BASE_PATH . '/pages';
$realPagesPath = realpath($pagesPath);
if ($realPagesPath === false) {
    $notFound();
}

$candidates = [];
if ($route === '') {
    $candidates[] = $pagesPath . '/index.php';
} elseif (preg_match('/\\.php\\z/', $route) === 1) {
    $candidates[] = $pagesPath . '/' . $route;
} else {
    $candidates[] = $pagesPath . '/' . $route . '.php';
    $candidates[] = $pagesPath . '/' . $route . '/index.php';
}

$selectedPage = null;
$pagesPrefix = rtrim($realPagesPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
foreach ($candidates as $candidate) {
    if (!is_file($candidate)) {
        continue;
    }

    $realCandidate = realpath($candidate);
    if ($realCandidate !== false && str_starts_with($realCandidate, $pagesPrefix)) {
        $selectedPage = $realCandidate;
        break;
    }
}

if ($selectedPage === null) {
    $notFound();
}

require_once $selectedPage;
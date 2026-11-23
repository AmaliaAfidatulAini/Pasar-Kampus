<?php
// COMPONENTS PROPS //
$title = $title ?? "";
$description = $description ?? "";
$styles = $styles ?? "";
// END OF COMPONENTS PROPS //

if (!headers_sent()) {
    header('Content-Security-Policy: ' . implode('; ', [
        "default-src 'self'",
        "script-src 'self'",
        "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
        "font-src 'self' https://fonts.gstatic.com",
        "img-src 'self' data: blob: https:",
        "connect-src 'self'",
        "object-src 'none'",
        "base-uri 'self'",
        "form-action 'self'",
        "frame-ancestors 'none'",
    ]));
}

$title = trim($title) !== '' ? trim($title) : 'Beranda';
$description = trim($description) !== '' ? trim($description) : 'Temukan barang prelove dengan mudah, harga terjangkau, dan transaksi langsung yang aman.';
$canonicalUrl = rtrim(BASEURL, '/') . current_request_path();
$shareImage = asset('img/cart-transparent.png');

$session = get_session();
$isLoggedIn = isset($session['user_id']);
$isAdmin = $isLoggedIn && ($session['role'] ?? '') === 'admin';
$searchQuery = is_string($_GET['q'] ?? null) ? trim($_GET['q']) : '';

$profileName = $isAdmin ? 'Administrator' : 'No Name';
$profileEmail = (string) ($session['email'] ?? '');
$avatarUrl = '';

if ($isLoggedIn) {
    $headerUser = executeQuery(
        'SELECT name, email, avatar_path FROM v_user_header WHERE user_id = ?',
        [$session['user_id']],
        'fetch'
    );

    if (is_array($headerUser)) {
        $profileName = trim((string) ($headerUser['name'] ?? '')) ?: $profileName;
        $profileEmail = trim((string) ($headerUser['email'] ?? '')) ?: $profileEmail;

        // Relative avatar rows live under public/uploads; upload() builds the URL.
        $avatarPath = trim((string) ($headerUser['avatar_path'] ?? ''));
        if ($avatarPath !== '') {
            $avatarUrl = preg_match('~^(https?:)?//|^/~', $avatarPath) === 1 ? $avatarPath : upload($avatarPath);
        }
    }
}

$avatarUrl = $avatarUrl !== '' ? $avatarUrl : asset('img/profile-placeholder.webp');

$currentRoute = is_string($_GET['url'] ?? null) ? trim(rawurldecode($_GET['url']), '/') : '';
$breadcrumbs = [];
$crumbPath = '';
foreach (explode('/', $currentRoute) as $segment) {
    if ($segment === '') {
        continue;
    }
    $crumbPath .= '/' . $segment;
    $breadcrumbs[] = [
        'label' => str_replace('-', ' ', preg_replace('/\.php$/', '', $segment)),
        'path' => $crumbPath,
    ];
}

$routePath = '/' . $currentRoute;
$isActive = static function (string $path) use ($routePath): bool {
    if ($path === '/') {
        return $routePath === '/';
    }
    return $routePath === $path || str_starts_with($routePath, $path . '/');
};

$navLinks = [
    ['label' => 'Beranda', 'href' => route('/'), 'active' => $isActive('/')],
    ['label' => 'Katalog', 'href' => route('/explore'), 'active' => $isActive('/explore')],
];
$showCart = $isLoggedIn && !$isAdmin;

$accountItems = $isAdmin
    ? [
        ['label' => 'Dashboard', 'href' => route('/admin'), 'icon' => 'layout-dashboard', 'active' => $isActive('/admin')],
    ]
    : [
        ['label' => 'Barang Saya', 'href' => route('/barang'), 'icon' => 'package', 'active' => $isActive('/barang')],
        ['label' => 'Tawaran Saya', 'href' => route('/tawaran/pembelian'), 'icon' => 'handshake', 'active' => $isActive('/tawaran/pembelian')],
        ['label' => 'Profil', 'href' => route('/user'), 'icon' => 'user-round', 'active' => $isActive('/user')],
    ];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> · PasarKampus</title>
    <meta name="description" content="<?= esc($description) ?>">
    <link rel="canonical" href="<?= esc($canonicalUrl) ?>">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#1D356D">
    <link rel="icon" href="<?= asset('img/favicon.ico') ?>" sizes="any">
    <link rel="icon" href="<?= asset('img/icon.svg') ?>" type="image/svg+xml">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="PasarKampus">
    <meta property="og:locale" content="id_ID">
    <meta property="og:title" content="<?= esc($title) ?> - PasarKampus">
    <meta property="og:description" content="<?= esc($description) ?>">
    <meta property="og:url" content="<?= esc($canonicalUrl) ?>">
    <meta property="og:image" content="<?= esc($shareImage) ?>">
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="<?= esc($title) ?> - PasarKampus">
    <meta name="twitter:description" content="<?= esc($description) ?>">
    <meta name="twitter:image" content="<?= esc($shareImage) ?>">
    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- CSS Stylesheet -->
    <link rel="stylesheet" href="<?= asset('css/lib/bootstrap.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/main.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/flasher.css') ?>">
    <?= $styles ?>
</head>
<body class="pk-body">

    <header class="navbar navbar-expand-lg pk-navbar sticky-top">
        <div class="container align-items-center gap-2 gap-sm-3 justify-content-lg-start">
            <a href="<?= route('/') ?>" class="navbar-brand pk-brand-logo flex-shrink-0" aria-label="PasarKampus">
                <img src="<?= asset('img/icon.svg') ?>" alt="PasarKampus" width="30" height="34">
            </a>

            <form class="pk-nav-search my-0" method="get" action="<?= esc(route('/explore')) ?>" role="search">
                <i data-lucide="search" class="pk-icon" aria-hidden="true"></i>
                <input type="search" name="q" id="pkHeaderSearch" class="form-control" placeholder="Search" aria-label="Cari barang" value="<?= esc($searchQuery) ?>">
                <button type="button" class="pk-search-clear<?= $searchQuery === '' ? ' d-none' : '' ?>" data-pk-search-clear="#pkHeaderSearch" aria-label="Hapus pencarian">
                    <i data-lucide="x" class="pk-icon" aria-hidden="true"></i>
                </button>
            </form>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#pkNavbar" aria-controls="pkNavbar" aria-expanded="false" aria-label="Buka navigasi">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="pkNavbar">
                <ul class="navbar-nav align-items-lg-center gap-2 ms-auto">
                    <?php foreach ($navLinks as $link): ?>
                        <li class="nav-item">
                            <a class="nav-link<?= $link['active'] ? ' active' : '' ?>" href="<?= esc($link['href']) ?>"<?= $link['active'] ? ' aria-current="page"' : '' ?>><?= esc($link['label']) ?></a>
                        </li>
                    <?php endforeach; ?>
                    <?php if ($showCart): ?>
                        <li class="nav-item">
                            <a class="nav-link d-inline-flex align-items-center<?= $isActive('/riwayat') ? ' active' : '' ?>" href="<?= esc(route('/riwayat/pembelian')) ?>" aria-label="Riwayat pembelian"<?= $isActive('/riwayat') ? ' aria-current="page"' : '' ?>>
                                <i data-lucide="shopping-cart" class="pk-icon" aria-hidden="true"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="pk-account d-flex align-items-center gap-3 flex-shrink-0">
                <?php if ($isLoggedIn): ?>
                    <div class="dropdown">
                        <button type="button" class="pk-avatar-btn" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Menu profil">
                            <span class="pk-avatar">
                                <img src="<?= esc($avatarUrl) ?>" alt="Foto profil <?= esc($profileName) ?>">
                            </span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <div class="px-3 pt-1 pb-2 border-bottom">
                                <div class="fw-bold text-truncate"><?= esc($profileName) ?></div>
                                <div class="small text-body-secondary text-truncate"><?= esc($profileEmail) ?></div>
                            </div>
                            <?php foreach ($accountItems as $item): ?>
                                <a href="<?= esc($item['href']) ?>" class="dropdown-item d-flex align-items-center gap-2<?= $item['active'] ? ' active' : '' ?>">
                                    <i data-lucide="<?= esc($item['icon']) ?>" class="pk-icon" aria-hidden="true"></i>
                                    <span><?= esc($item['label']) ?></span>
                                </a>
                            <?php endforeach; ?>
                            <hr class="my-2">
                            <a href="<?= esc(route('/logout')) ?>" class="dropdown-item d-flex align-items-center gap-2 text-danger" data-pk-confirm="Apakah Anda yakin ingin keluar?" data-pk-confirm-title="Logout">
                                <i data-lucide="log-out" class="pk-icon" aria-hidden="true"></i>
                                <span>Keluar</span>
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?= esc(route('/login')) ?>" class="btn btn-light fw-semibold">Masuk</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <?php if ($breadcrumbs !== []): ?>
        <nav class="pk-breadcrumb" aria-label="Breadcrumb">
            <div class="container">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a class="text-primary" href="<?= esc(route('/')) ?>">Beranda</a>
                    </li>
                    <?php foreach ($breadcrumbs as $index => $crumb): ?>
                        <?php if ($index === array_key_last($breadcrumbs)): ?>
                            <li class="breadcrumb-item active" aria-current="page">
                                <span class="fw-bold text-primary"><?= esc($crumb['label']) ?></span>
                            </li>
                        <?php else: ?>
                            <li class="breadcrumb-item">
                                <a class="text-primary" href="<?= esc(route($crumb['path'])) ?>"><?= esc($crumb['label']) ?></a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ol>
            </div>
        </nav>
    <?php endif; ?>

    <?php Component::include('common/flasher'); ?>

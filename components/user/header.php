<?php
// COMPONENTS PROPS //
$title = $title ?? "";
$description = $description ?? "";
$styles = $styles ?? "";
// END OF COMPONENTS PROPS //

$title = trim($title) !== '' ? trim($title) : 'Beranda';
$description = trim($description) !== '' ? trim($description) : 'Temukan barang prelove dengan mudah, harga terjangkau, dan transaksi langsung yang aman.';
$canonicalUrl = rtrim(BASEURL, '/') . current_request_path();
$shareImage = asset('img/cart-transparent.png');

$session = get_session();
$profileName = ($session['role'] ?? '') === 'admin' ? 'Administrator' : 'No Name';
$searchQuery = is_string($_GET['q'] ?? null) ? trim($_GET['q']) : '';
$avatarUrl = '';

if ($session && isset($session['user_id'])) {
    $profile = executeQuery(
        'SELECT p.name, a.file_path AS avatar_path
         FROM user_profiles p
         LEFT JOIN attachments a ON a.attachment_id = p.attachment_id
         WHERE p.user_id = ?
         ORDER BY p.profile_id DESC
         LIMIT 1',
        [$session['user_id']],
        'fetch'
    ) ?: [];

    $profileName = trim((string) ($profile['name'] ?? '')) ?: $profileName;

    // Login stores no avatar, so the header resolves it from the profile row.
    // Older rows may hold a path relative to public/uploads; upload() turns it into a URL.
    $avatarPath = trim((string) ($profile['avatar_path'] ?? ''));
    if ($avatarPath !== '') {
        $avatarUrl = preg_match('~^(https?:)?//|^/~', $avatarPath) === 1 ? $avatarPath : upload($avatarPath);
    }
}

$avatarUrl = $avatarUrl !== '' ? $avatarUrl : trim((string) ($session['avatar_url'] ?? ''));
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
        return $routePath === '/' || $routePath === '/explore';
    }
    return $routePath === $path || str_starts_with($routePath, $path . '/');
};

$isAdmin = ($session['role'] ?? '') === 'admin';

$navLinks = [
    ['label' => 'Home', 'href' => route('/'), 'active' => $isActive('/')],
];
if (!$isAdmin) {
    $navLinks[] = ['label' => 'About', 'href' => route('/#faq'), 'active' => false];
}
if ($session) {
    $navLinks[] = ['label' => 'Riwayat', 'href' => route('/riwayat'), 'active' => $isActive('/riwayat') || $isActive('/tawaran')];
}

$isBarangPage = $isActive('/barang');

$accountLink = $isAdmin
    ? ['label' => 'Dashboard', 'href' => route('/admin'), 'active' => $isActive('/admin')]
    : null;
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
                        <?php if ($link['label'] === 'Riwayat'): ?>
                            <li class="nav-item dropdown">
                                <button type="button" class="nav-link dropdown-toggle<?= $link['active'] ? ' active' : '' ?>" data-bs-toggle="dropdown" aria-expanded="false">Riwayat</button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <span class="pk-dropdown-label">
                                            <i data-lucide="receipt-text" class="pk-icon" aria-hidden="true"></i>
                                            <span>Transaksi</span>
                                        </span>
                                    </li>
                                    <li><a class="dropdown-item" href="<?= esc(route('/riwayat/pembelian')) ?>">Pembelian</a></li>
                                    <li><a class="dropdown-item" href="<?= esc(route('/riwayat/penjualan')) ?>">Penjualan</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <span class="pk-dropdown-label">
                                            <i data-lucide="handshake" class="pk-icon" aria-hidden="true"></i>
                                            <span>Tawaran</span>
                                        </span>
                                    </li>
                                    <li><a class="dropdown-item" href="<?= esc(route('/tawaran/pembelian')) ?>">Pembelian</a></li>
                                    <li><a class="dropdown-item" href="<?= esc(route('/tawaran/penjualan')) ?>">Penjualan</a></li>
                                </ul>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link<?= $link['active'] ? ' active' : '' ?>" href="<?= esc($link['href']) ?>"<?= $link['active'] ? ' aria-current="page"' : '' ?>><?= esc($link['label']) ?></a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <?php if ($accountLink !== null): ?>
                        <li class="nav-item">
                            <a class="nav-link<?= $accountLink['active'] ? ' active' : '' ?>" href="<?= esc($accountLink['href']) ?>"><?= esc($accountLink['label']) ?></a>
                        </li>
                    <?php endif; ?>
                    <?php if ($session): ?>
                        <li class="nav-item dropdown d-lg-none">
                            <button type="button" class="nav-link dropdown-toggle w-100 text-start" data-bs-toggle="dropdown" aria-expanded="false">
                                Akun
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item d-flex align-items-center gap-2<?= $isBarangPage ? ' active' : '' ?>" href="<?= esc(route('/barang')) ?>">
                                    <i data-lucide="package" class="pk-icon" aria-hidden="true"></i><span>Barang Saya</span></a>
                                </li>
                                <li><a class="dropdown-item d-flex align-items-center gap-2" href="<?= esc(route('/user')) ?>">
                                    <i data-lucide="user-round" class="pk-icon" aria-hidden="true"></i><span>Profil</span></a>
                                </li>
                                <li><a class="dropdown-item d-flex align-items-center gap-2 text-danger" href="<?= esc(route('/logout')) ?>" data-pk-confirm="Apakah Anda yakin ingin keluar dari akun PasarKampus?" data-pk-confirm-title="Keluar dari akun?"><i data-lucide="log-out" class="pk-icon" aria-hidden="true"></i><span>Keluar</span></a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="<?= $session ? 'd-none d-lg-flex' : 'd-flex' ?> align-items-center gap-3 flex-shrink-0">
                <?php if ($session): ?>
                    <div class="dropdown">
                        <button type="button" class="pk-avatar-btn" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Menu profil">
                            <span class="pk-avatar">
                                <img src="<?= esc($avatarUrl) ?>" alt="Foto profil <?= esc($profileName) ?>">
                            </span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <div class="px-3 pt-1 pb-2 border-bottom">
                                <div class="fw-bold text-truncate"><?= esc($profileName) ?></div>
                                <div class="small text-body-secondary text-truncate"><?= esc((string) ($session['email'] ?? '')) ?></div>
                            </div>
                            <a href="<?= esc(route('/barang')) ?>" class="dropdown-item d-flex align-items-center gap-2">
                                <i data-lucide="package" class="pk-icon" aria-hidden="true"></i>
                                <span>Barang Saya</span>
                            </a>
                            <a href="<?= esc(route('/user')) ?>" class="dropdown-item d-flex align-items-center gap-2">
                                <i data-lucide="user-round" class="pk-icon" aria-hidden="true"></i>
                                <span>Profil</span>
                            </a>
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
                        <a class="text-primary" href="<?= esc(route('/')) ?>">home</a>
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

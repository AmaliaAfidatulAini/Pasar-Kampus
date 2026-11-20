<?php
// COMPONENTS PROPS //
$activeTab = $activeTab ?? "";
$title = $title ?? "";
$subtitle = $subtitle ?? "";
$actionBtn = $actionBtn ?? null;
// END OF COMPONENTS PROPS //

$session = get_session();
$username = trim((string) ($session['name'] ?? 'Administrator'));
$email = trim((string) ($session['email'] ?? '-'));
$username_alias = strtoupper(substr($username, 0, 1));

$navigation_links = [
    'dashboard' => ['Dashboard', '/admin/', 'layout-dashboard'],
    'users' => ['Users', '/admin/users', 'users-round'],
    'kategori' => ['Kategori', '/admin/kategori', 'tags'],
    'tags' => ['Tags', '/admin/tag', 'hash'],
    'barang' => ['Barang', '/admin/barang', 'package'],
    'pengajuan' => ['Pengajuan', '/admin/pengajuan', 'clipboard-list'],
    'report' => ['Laporan', '/admin/laporan', 'flag'],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PasarKampus | <?= esc($title) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('/css/admin.css') ?>">
    <link rel="stylesheet" href="<?= asset('/css/flasher.css') ?>">
    <link rel="stylesheet" href="https://cdn.datatables.net/3.1.5/css/dataTables.dataTables.min.css">
</head>
<body class="admin-body">
    <div class="admin-layout">
        <script>
            (function () {
                try {
                    if (localStorage.getItem('admin-sidebar-collapsed') === 'true') {
                        document.querySelector('.admin-layout').classList.add('sidebar-collapsed');
                    }
                    document.querySelector('.admin-layout').classList.add('sidebar-state-preload');
                } catch (error) {}
            }());
        </script>
        <aside id="adminSidebar" class="admin-sidebar" aria-label="Navigasi admin">
            <div class="admin-sidebar-top">
                <div class="admin-profile-box">
                    <div class="admin-profile-avatar-wrap" aria-hidden="true"><span class="admin-profile-initial"><?= esc($username_alias) ?></span></div>
                    <div class="admin-profile-info">
                        <div class="admin-profile-name"><?= esc($username) ?></div>
                        <?php if ($email !== ''): ?>
                            <span class="admin-profile-email"><?= esc($email) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <nav class="admin-nav-section" aria-label="Menu admin">
                    <ul class="admin-nav-list">
                        <?php foreach ($navigation_links as $key => [$label, $href, $icon]): ?>
                            <li>
                                <a id="nav-<?= esc($key) ?>" href="<?= route($href) ?>" class="admin-nav-link<?= $activeTab === $key ? ' active' : '' ?>" aria-label="<?= esc($label) ?>"<?= $activeTab === $key ? ' aria-current="page"' : '' ?>>
                                    <span class="admin-nav-icon" aria-hidden="true"><i data-lucide="<?= esc($icon) ?>"></i></span>
                                    <span class="admin-nav-label"><?= esc($label) ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </nav>
            </div>
            <div class="admin-sidebar-footer">
                <a class="admin-btn-logout" href="<?= route('/logout') ?>" aria-label="Keluar"><i data-lucide="log-out" aria-hidden="true"></i><span class="admin-nav-label">Keluar</span></a>
            </div>
        </aside>
        <button type="button" class="admin-sidebar-overlay" onclick="toggleMobileSidebar(false)" aria-label="Tutup navigasi admin"></button>
        <main class="admin-main">
            <header class="admin-topbar">
                <div class="admin-topbar-left">
                    <button type="button" class="admin-mobile-toggle is-open" onclick="toggleMobileSidebar()" aria-label="Ciutkan navigasi admin" aria-controls="adminSidebar" aria-expanded="true">
                        <span class="hamburger-line"></span>
                        <span class="hamburger-line"></span>
                        <span class="hamburger-line"></span>
                    </button>
                    <div class="admin-breadcrumb">
                        <span>Admin / </span><strong id="adminCurrentBreadcrumb"><?= ucwords(esc($activeTab)) ?></strong>
                    </div>
                </div>
            </header>

            <?php Component::include('common/flasher'); ?>
            
            <div class="admin-content-container">
                <section class="admin-page-header" aria-labelledby="admintitle">
                    <div class="admin-header-title-wrap">
                        <h1 id="admintitle"><?= esc($title) ?></h1>
                        <p><?= esc($subtitle) ?></p>
                    </div>
                    <?= $actionBtn ?>
                </section>


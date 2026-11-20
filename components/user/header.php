<?php
// COMPONENTS PROPS //
$title = $title ?? "";
$styles = $styles ?? "";
// END OF COMPONENTS PROPS //

$session = get_session();
$profileName = ($session['role'] ?? '') === 'admin' ? 'Administrator' : 'No Name';

if ($session && isset($session['user_id'])) {
    $profile = executeQuery(
        'SELECT name FROM user_profiles WHERE user_id = ? ORDER BY profile_id DESC LIMIT 1',
        [$session['user_id']],
        'fetch'
    ) ?: [];

    $profileName = trim((string) ($profile['name'] ?? '')) ?: $profileName;
}

$initial = strtoupper(substr($profileName, 0, 1));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PasarKampus - <?= $title ?></title>
    <meta name="description" content="Temukan barang prelove dengan mudah, harga terjangkau, dan transaksi langsung yang aman.">
    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- CSS Stylesheet -->
    <link rel="stylesheet" href="<?= asset("css/main.css") ?>">
    <link rel="stylesheet" href="<?= asset("css/flasher.css") ?>">
    <?= $styles ?>
</head>
<body class="lp-body">

    <!-- ================= HEADER / NAVBAR (Wireframe 1 & Wireframe 2) ================= -->
    <header class="lp-navbar">
        <div class="lp-container lp-nav-wrap">
            <div class="lp-nav-left">
                <a href="<?= route('/') ?>" class="lp-logo">
                    PasarKampus
                </a>
                <form class="lp-nav-search" method="GET" action="<?= esc(route('/barang')) ?>">
                    <span class="lp-nav-search-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </span>
                    <input type="search" name="q" id="lpHeaderSearch" class="lp-nav-search-input" placeholder="Search">
                </form>
            </div>

            <div class="lp-nav-right-area">
                <?php if ($session): ?>
                    <div id="lpHeaderAuth" class="lp-header-state">
                        <?php if (($session['role'] ?? '') === 'admin'): ?>
                            <a href="<?= esc(route('/admin')) ?>" class="lp-btn-masuk">
                                Dashboard
                            </a>
                        <?php else: ?>
                            <a href="#faq" class="lp-help-link" onclick="handleHelpClick(event)">
                                butuh bantuan?
                            </a>
                        <?php endif; ?>
                        <div class="lp-profile-wrapper">
                            <button type="button" id="lpProfileBtn" class="lp-profile-btn" onclick="toggleProfileDropdown(event)" aria-haspopup="true" aria-expanded="false" title="Menu Profil">
                                <div class="lp-profile-circle" id="lpAvatarCircle" style="<?= !empty($session['avatar_url']) ? 'padding:0; overflow:hidden;' : '' ?>">
                                    <?php if (!empty($session['avatar_url'])): ?>
                                        <img src="<?= esc($session['avatar_url']) ?>" alt="Avatar" style="width:100%; height:100%; border-radius:50%; object-fit:cover;">
                                    <?php else: ?>
                                        <span id="lpAvatarInitial" class="lp-avatar-initial"><?= esc($initial) ?></span>
                                    <?php endif; ?>
                                </div>
                            </button>

                            <div id="lpProfileDropdown" class="lp-profile-dropdown">
                                <div style="padding: 10px 18px 12px; border-bottom: 1px solid #E2E8F0;">
                                    <strong style="display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= esc($profileName) ?></strong>
                                    <span style="display: block; color: #64748B; font-size: 0.82rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= esc((string) ($session['email'] ?? '')) ?></span>
                                </div>
                                <a href="<?= route('/dashboard_penjual') ?>" class="lp-dropdown-item">
                                    <i data-lucide="package" class="lp-dropdown-icon" aria-hidden="true"></i>
                                    <span>Daftar Barang</span>
                                </a>
                                <a href="<?= route('/pengajuan') ?>" class="lp-dropdown-item">
                                    <i data-lucide="history" class="lp-dropdown-icon" aria-hidden="true"></i>
                                    <span>Riwayat Pengajuan</span>
                                </a>
                                <a href="<?= route('/profile') ?>" class="lp-dropdown-item">
                                    <i data-lucide="user-round" class="lp-dropdown-icon" aria-hidden="true"></i>
                                    <span>Profil Pengguna</span>
                                </a>
                                <div class="lp-dropdown-divider"></div>
                                <a href="<?= route('/logout') ?>" class="lp-dropdown-item lp-dropdown-logout" onclick="return confirm('Apakah Anda yakin ingin keluar dari akun PasarKampus?');">
                                    <i data-lucide="log-out" class="lp-dropdown-icon" aria-hidden="true"></i>
                                    <span>Logout</span>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div id="lpHeaderGuest" class="lp-header-state">
                        <a href="<?= route('/login') ?>" class="lp-btn-masuk" style="text-decoration: none; display: inline-flex; align-items: center; justify-content: center;">
                            Masuk
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <?php Component::include('common/flasher'); ?>

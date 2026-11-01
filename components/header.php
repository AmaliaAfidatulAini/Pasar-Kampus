<?php
$title = $title ?? '';
$session = get_session();
$displayName = $session['name'] ?? $session['email'] ?? 'Pengguna';
$initial = strtoupper(substr($displayName, 0, 1));
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
    <link rel="stylesheet" href="<?= asset("css/style.css") ?>">
    <link rel="stylesheet" href="<?= asset("css/flasher.css") ?>">
</head>
<body class="lp-body">

    <!-- ================= HEADER / NAVBAR (Wireframe 1 & Wireframe 2) ================= -->
    <header class="lp-navbar">
        <div class="lp-container lp-nav-wrap">
            <div class="lp-nav-left">
                <a href="<?= route('/') ?>" class="lp-logo">
                    PasarKampus
                </a>
                <div class="lp-nav-search">
                    <span class="lp-nav-search-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </span>
                    <input type="text" id="lpHeaderSearch" class="lp-nav-search-input" placeholder="Search" onkeydown="handleHeaderSearch(event)">
                </div>
            </div>

            <div class="lp-nav-right-area">
                <?php if ($session): ?>
                    <div id="lpHeaderAuth" class="lp-header-state">
                        <a href="#faq" class="lp-help-link" onclick="handleHelpClick(event)">
                            butuh bantuan?
                        </a>

                        <div class="lp-profile-wrapper">
                            <button type="button" id="lpProfileBtn" class="lp-profile-btn" onclick="toggleProfileDropdown(event)" aria-haspopup="true" aria-expanded="false" title="Menu Profil">
                                <div class="lp-profile-circle" id="lpAvatarCircle">
                                    <span id="lpAvatarInitial" class="lp-avatar-initial"><?= esc($initial) ?></span>
                                </div>
                            </button>

                            <div id="lpProfileDropdown" class="lp-profile-dropdown">
                                <a href="<?= route('/barang') ?>" class="lp-dropdown-item">
                                    <span>Daftar Barang Saya</span>
                                </a>
                                <a href="<?= route('/laporan') ?>" class="lp-dropdown-item">
                                    <span>Riwayat Pengajuan</span>
                                </a>
                                <button type="button" class="lp-dropdown-item" onclick="openProfileModal()">
                                    <span>Profil Pengguna</span>
                                </button>
                                <div class="lp-dropdown-divider"></div>
                                <a href="<?= route('/logout') ?>" class="lp-dropdown-item lp-dropdown-logout" onclick="return confirm('Apakah Anda yakin ingin keluar dari akun PasarKampus?');">
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

    <?php include_component('flasher'); ?>

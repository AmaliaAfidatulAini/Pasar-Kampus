<?php
$session = get_session();
$isLoggedIn = !empty($session);
$userName = $session['name'] ?? 'Pengguna';
$userRole = $session['role'] ?? 'user';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PasarKampus - Pusat Transaksi Mahasiswa</title>
    <!-- Google Fonts Inter & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- CSS Stylesheet -->
    <link rel="stylesheet" href="<?= route('/css/style.css') ?>">
    
    <style>
        :root {
            --pk-nav-blue: #2355B8;
            --pk-navy: #1D356D;
            --pk-amber: #F59E0B;
            --pk-amber-dark: #D97706;
            --pk-blue-primary: #2563EB;
            --pk-border: #E2E8F0;
            --pk-bg: #F8FAFC;
        }

        body.pk-dashboard-body {
            background-color: var(--pk-bg);
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
            color: #1E293B;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* 1. TOP NAVBAR */
        .pk-navbar {
            background-color: var(--pk-nav-blue);
            height: 64px;
            display: flex;
            align-items: center;
            padding: 0 28px;
            color: #FFFFFF;
            position: sticky;
            top: 0;
            z-index: 900;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .pk-nav-inner {
            width: 100%;
            max-width: 1280px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .pk-nav-logo {
            font-size: 1.35rem;
            font-weight: 800;
            color: #FFFFFF;
            text-decoration: none;
            letter-spacing: -0.5px;
            flex-shrink: 0;
        }

        .pk-nav-search {
            flex: 1;
            max-width: 360px;
            position: relative;
        }

        .pk-nav-search input {
            width: 100%;
            height: 38px;
            border-radius: 9999px;
            border: none;
            padding: 0 16px 0 38px;
            font-size: 0.82rem;
            outline: none;
            background: #FFFFFF;
            color: #1E293B;
            box-sizing: border-box;
            font-family: inherit;
        }

        .pk-nav-search input::placeholder {
            color: #94A3B8;
        }

        .pk-nav-search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748B;
            pointer-events: none;
            display: flex;
            align-items: center;
        }

        .pk-nav-link {
            color: #FFFFFF;
            text-decoration: none;
            font-size: 0.88rem;
            font-weight: 600;
            transition: opacity 0.2s;
        }

        .pk-nav-link:hover {
            opacity: 0.85;
        }

        /* 2. MAIN LAYOUT & WHITE CONTAINER */
        .pk-main-wrapper {
            flex: 1;
            padding: 24px 20px 48px;
            box-sizing: border-box;
        }

        .pk-content-card {
            max-width: 1280px;
            margin: 0 auto;
            background: #FFFFFF;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.05);
            border: 1px solid var(--pk-border);
            padding: 28px;
            box-sizing: border-box;
        }

        /* 3. HERO BANNER */
        .pk-hero-banner {
            background-color: var(--pk-navy);
            border-radius: 16px;
            padding: 28px 36px;
            color: #FFFFFF;
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            align-items: center;
            gap: 32px;
            margin-bottom: 24px;
            position: relative;
            overflow: hidden;
        }

        .pk-hero-left h1 {
            font-size: 2.1rem;
            font-weight: 800;
            line-height: 1.25;
            margin: 0 0 12px 0;
            letter-spacing: -0.5px;
        }

        .pk-hero-highlight {
            color: var(--pk-amber);
        }

        .pk-hero-desc {
            font-size: 0.86rem;
            color: #CBD5E1;
            line-height: 1.55;
            margin: 0 0 20px 0;
            max-width: 480px;
        }

        .pk-hero-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .pk-pill-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(4px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #FFFFFF;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 9999px;
        }

        /* HERO RIGHT */
        .pk-hero-right {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }

        .pk-featured-tag {
            border: 1.5px solid var(--pk-amber);
            color: var(--pk-amber);
            font-size: 0.72rem;
            font-weight: 800;
            padding: 4px 14px;
            border-radius: 8px;
            margin-bottom: 8px;
            background: #FFFBEB;
            letter-spacing: 0.2px;
        }

        .pk-featured-card {
            background: #FFFFFF;
            border: 1px solid var(--pk-border);
            border-radius: 14px;
            padding: 12px;
            width: 100%;
            max-width: 270px;
            box-sizing: border-box;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.04);
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .pk-featured-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
        }

        /* 4. FILTER TOOLBAR */
        .pk-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 20px 0 16px;
        }

        .pk-filter-btn {
            background: transparent;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.92rem;
            font-weight: 700;
            color: #1E293B;
            padding: 6px 12px 6px 0;
            border-radius: 8px;
            transition: color 0.2s;
            font-family: inherit;
        }

        .pk-filter-btn:hover {
            color: var(--pk-blue-primary);
        }

        .pk-filter-count {
            font-size: 0.8rem;
            font-weight: 700;
            color: #1E293B;
        }

        /* 5. PRODUCT GRID */
        .pk-product-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 32px;
        }

        .pk-card {
            background: #FFFFFF;
            border: 1px solid var(--pk-border);
            border-radius: 14px;
            padding: 12px;
            display: flex;
            flex-direction: column;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
            box-sizing: border-box;
        }

        .pk-card:hover {
            transform: translateY(-3px);
            border-color: #CBD5E1;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
        }

        .pk-card-top-badges {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .pk-badge-condition {
            background-color: var(--pk-navy);
            color: #FFFFFF;
            font-size: 0.65rem;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 5px;
        }

        .pk-badge-category {
            border: 1px solid #3B82F6;
            color: #2563EB;
            font-size: 0.65rem;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 5px;
            background: #EFF6FF;
        }

        .pk-card-img-wrap {
            width: 100%;
            height: 124px;
            border-radius: 8px;
            overflow: hidden;
            background: #F1F5F9;
            margin-bottom: 8px;
        }

        .pk-card-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.3s ease;
        }

        .pk-card:hover .pk-card-img-wrap img {
            transform: scale(1.05);
        }

        .pk-card-location {
            background: #BFDBFE;
            color: var(--pk-navy);
            font-size: 0.64rem;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 4px;
            display: inline-block;
            margin-bottom: 6px;
            align-self: flex-start;
        }

        .pk-card-title {
            font-size: 0.86rem;
            font-weight: 800;
            color: #0F172A;
            margin: 0 0 4px 0;
            line-height: 1.35;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 2.3em;
        }

        .pk-card-desc {
            font-size: 0.72rem;
            color: #64748B;
            line-height: 1.45;
            margin: 0 0 10px 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            flex-grow: 1;
        }

        .pk-card-orig-price {
            font-size: 0.7rem;
            color: #94A3B8;
            text-decoration: line-through;
            font-weight: 600;
            line-height: 1;
        }

        .pk-card-price {
            font-size: 1rem;
            font-weight: 800;
            color: var(--pk-blue-primary);
            line-height: 1.3;
        }

        /* 6. PAGINATION */
        .pk-pagination {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin: 20px 0 36px;
        }

        .pk-page-btn {
            min-width: 34px;
            height: 34px;
            padding: 0 6px;
            border-radius: 8px;
            border: 1px solid var(--pk-border);
            background: #FFFFFF;
            color: #1E293B;
            font-size: 0.82rem;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s;
            font-family: inherit;
        }

        .pk-page-btn:hover {
            border-color: var(--pk-blue-primary);
            color: var(--pk-blue-primary);
        }

        .pk-page-btn.active {
            background-color: var(--pk-navy);
            border-color: var(--pk-navy);
            color: #FFFFFF;
        }

        /* 7. KONDISI BARANG SLIDER */
        .pk-condition-section {
            border-top: 1px solid var(--pk-border);
            padding-top: 24px;
            margin-top: 16px;
        }

        .pk-condition-title {
            font-size: 0.88rem;
            font-weight: 800;
            color: #1E293B;
            margin-bottom: 12px;
        }

        .pk-condition-slider-wrap {
            position: relative;
            padding: 0 8px;
        }

        .pk-condition-slider {
            width: 100%;
            height: 6px;
            border-radius: 9999px;
            background: #E2E8F0;
            outline: none;
            -webkit-appearance: none;
            cursor: pointer;
        }

        .pk-condition-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: var(--pk-navy);
            border: 2px solid #FFFFFF;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
            cursor: pointer;
        }

        .pk-condition-labels {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            font-size: 0.76rem;
            font-weight: 700;
            color: #64748B;
        }

        .pk-condition-labels span {
            cursor: pointer;
            user-select: none;
            transition: color 0.2s;
        }

        .pk-condition-labels span:hover {
            color: var(--pk-navy);
        }

        /* 8. FILTER DRAWER */
        .pk-sidebar-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.45);
            backdrop-filter: blur(2px);
            z-index: 1000;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.25s ease;
        }

        .pk-sidebar-backdrop.active {
            opacity: 1;
            pointer-events: auto;
        }

        .pk-sidebar-drawer {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 320px;
            max-width: 85vw;
            background: #FFFFFF;
            z-index: 1001;
            box-shadow: 8px 0 30px rgba(15, 23, 42, 0.15);
            transform: translateX(-100%);
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            display: flex;
            flex-direction: column;
        }

        .pk-sidebar-drawer.active {
            transform: translateX(0);
        }

        .pk-sidebar-header {
            padding: 20px 22px;
            border-bottom: 1px solid var(--pk-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .pk-sidebar-title {
            font-size: 1.05rem;
            font-weight: 800;
            color: #0F172A;
            margin: 0;
            letter-spacing: -0.3px;
        }

        .pk-sidebar-close {
            background: none;
            border: none;
            font-size: 1.2rem;
            color: #64748B;
            cursor: pointer;
            padding: 4px;
            line-height: 1;
        }

        .pk-sidebar-body {
            flex: 1;
            overflow-y: auto;
            padding: 20px 22px;
            display: flex;
            flex-direction: column;
            gap: 22px;
        }

        .pk-filter-group-title {
            font-size: 0.72rem;
            font-weight: 800;
            color: #94A3B8;
            letter-spacing: 0.6px;
            margin-bottom: 10px;
        }

        .pk-filter-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .pk-filter-item {
            font-size: 0.84rem;
            font-weight: 600;
            color: #334155;
            padding: 6px 10px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.15s;
            user-select: none;
        }

        .pk-filter-item:hover {
            background: #F1F5F9;
            color: var(--pk-navy);
        }

        .pk-filter-item.active {
            background: #EFF6FF;
            color: var(--pk-blue-primary);
            font-weight: 700;
        }

        .pk-see-more-link {
            font-size: 0.74rem;
            color: var(--pk-blue-primary);
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            display: inline-block;
            margin-top: 4px;
        }

        .pk-checkbox-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.8rem;
            color: #334155;
            cursor: pointer;
        }

        .pk-price-inputs {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .pk-price-col label {
            display: block;
            font-size: 0.7rem;
            font-weight: 700;
            color: #64748B;
            margin-bottom: 4px;
        }

        .pk-price-col input {
            width: 100%;
            height: 34px;
            border: 1px solid var(--pk-border);
            border-radius: 6px;
            padding: 0 10px;
            font-size: 0.78rem;
            outline: none;
            box-sizing: border-box;
            font-family: inherit;
        }

        .pk-reset-btn {
            background-color: var(--pk-navy);
            color: #FFFFFF;
            border: none;
            border-radius: 8px;
            padding: 9px 18px;
            font-size: 0.8rem;
            font-weight: 700;
            cursor: pointer;
            align-self: flex-start;
            margin-top: 10px;
            font-family: inherit;
        }

        @media (max-width: 1024px) {
            .pk-product-grid { grid-template-columns: repeat(3, 1fr); }
            .pk-hero-banner { grid-template-columns: 1fr; gap: 24px; }
            .pk-hero-right { align-items: flex-start; }
            .pk-featured-card { max-width: 100%; }
        }

        @media (max-width: 768px) {
            .pk-product-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
            .pk-content-card { padding: 20px 16px; }
            .pk-hero-banner { padding: 20px; }
            .pk-hero-left h1 { font-size: 1.6rem; }
            .pk-nav-search { max-width: 200px; }
        }

        @media (max-width: 480px) {
            .pk-product-grid { grid-template-columns: 1fr; }
            .pk-navbar { padding: 0 16px; }
        }
    </style>
</head>
<body class="pk-dashboard-body">

    <!-- ================= 1. TOP NAVBAR ================= -->
    <header class="pk-navbar">
        <div class="pk-nav-inner">
            <a href="<?= route('/') ?>" class="pk-nav-logo">
                PasarKampus
            </a>

            <div class="pk-nav-search">
                <span class="pk-nav-search-icon">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </span>
                <input type="text" id="dashSearch" oninput="applyFilters()" placeholder="Cari barang....">
            </div>

            <div style="display: flex; align-items: center; gap: 18px;">
                <a href="<?= route('/riwayat.php') ?>" class="pk-nav-link">Riwayat</a>
                <a href="<?= route('/#faq') ?>" class="pk-nav-link">butuh bantuan?</a>
                <div style="position: relative;">
                    <button type="button" onclick="toggleDashDropdown(event)" style="background: none; border: none; cursor: pointer; padding: 0; outline: none;">
                        <div style="width: 38px; height: 38px; border-radius: 50%; background-color: #94A3B8; border: 2px solid rgba(255,255,255,0.4); overflow: hidden; display: flex; align-items: center; justify-content: center; color: #FFFFFF; font-weight: 700;">
                            <img id="dashAvatarImg" src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&q=80&w=200" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                    </button>
                    <div id="dashDropdown" style="position: absolute; top: calc(100% + 14px); right: 0; width: 220px; background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 12px; box-shadow: 0 12px 30px rgba(15,23,42,0.18); display: none; padding: 8px 0; z-index: 1000;">
                        <div style="position: absolute; top: -6px; right: 13px; width: 12px; height: 12px; background: #FFFFFF; border-left: 1px solid #E2E8F0; border-top: 1px solid #E2E8F0; transform: rotate(45deg);"></div>
                        <?php if ($userRole === 'admin'): ?>
                            <a href="<?= route('/dashboard/index.php') ?>" style="color: #1E293B; display: flex; align-items: center; gap: 10px; padding: 11px 18px; text-decoration: none; font-size: 0.86rem; font-weight: 600;">
                                <span>⚙️</span>
                                <span>Portal Admin</span>
                            </a>
                        <?php endif; ?>
                        <a href="<?= route('/dashboard-penjual.php') ?>" style="color: #1E293B; display: flex; align-items: center; gap: 10px; padding: 11px 18px; text-decoration: none; font-size: 0.86rem; font-weight: 600;">
                            <span>📦</span>
                            <span>Daftar Barang Saya</span>
                        </a>
                        <a href="<?= route('/riwayat.php') ?>" style="color: #1E293B; display: flex; align-items: center; gap: 10px; padding: 11px 18px; text-decoration: none; font-size: 0.86rem; font-weight: 600;">
                            <span>📑</span>
                            <span>Riwayat Pengajuan</span>
                        </a>
                        <a href="<?= route('/profil.php') ?>" style="color: #1E293B; display: flex; align-items: center; gap: 10px; padding: 11px 18px; text-decoration: none; font-size: 0.86rem; font-weight: 600;">
                            <span>👤</span>
                            <span>Profil Pengguna (<?= esc($userName) ?>)</span>
                        </a>
                        <div style="height: 1px; background-color: #E2E8F0; margin: 6px 0;"></div>
                        <a href="<?= route('/logout.php') ?>" style="color: #DC2626; display: flex; align-items: center; gap: 10px; padding: 11px 18px; text-decoration: none; font-size: 0.86rem; font-weight: 600;">
                            <span>🚪</span>
                            <span>Logout</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- ================= 2. MAIN DASHBOARD CONTENT ================= -->
    <main class="pk-main-wrapper">
        <div class="pk-content-card">

            <!-- Hero Banner -->
            <section class="pk-hero-banner">
                <div class="pk-hero-left">
                    <h1>
                        Cari Barang Bekas<br>
                        <span class="pk-hero-highlight">Berkualitas</span> &amp; Murah!
                    </h1>
                    <p class="pk-hero-desc">
                        Temukan buku kuliah, elektronik, perlengkapan kost, ATK, hingga pakaian preloved dari teman sekampus dan sekitar domisili Anda dengan aman &amp; mudah.
                    </p>
                    <div class="pk-hero-pills">
                        <div class="pk-pill-badge"><span>🎓</span> 100% Khusus Mahasiswa</div>
                        <div class="pk-pill-badge"><span>🤝</span> Aman COD Area Kampus</div>
                        <div class="pk-pill-badge"><span>💸</span> Harga terjangkau</div>
                    </div>
                </div>

                <div class="pk-hero-right">
                    <span class="pk-featured-tag">Baru Diunggah</span>
                    <div class="pk-featured-card" onclick="openProductDetail(1)">
                        <div class="pk-card-top-badges">
                            <span class="pk-badge-condition">Layak pakai</span>
                            <span class="pk-badge-category">Elektronik</span>
                        </div>
                        <div class="pk-card-img-wrap" style="height: 130px;">
                            <img src="https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&q=80&w=600" alt="Keyboard Logiac - Silver">
                        </div>
                        <span class="pk-card-location">📍 Kecamatan Lowokwaru</span>
                        <h3 class="pk-card-title">Keyboard Logiac - Silver</h3>
                        <p class="pk-card-desc">Keyboard wireless dengan balutan warna silver elegan dan desain slim yang hemat tempat...</p>
                        <div class="pk-card-orig-price">Rp. 150.000</div>
                        <div class="pk-card-price">Rp. 90.000</div>
                    </div>
                </div>
            </section>

            <!-- Toolbar: Filter Button & Item Count -->
            <div class="pk-toolbar">
                <button type="button" class="pk-filter-btn" onclick="openFilterSidebar()">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="4" y1="21" x2="4" y2="14"></line>
                        <line x1="4" y1="10" x2="4" y2="3"></line>
                        <line x1="12" y1="21" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12" y2="3"></line>
                        <line x1="20" y1="21" x2="20" y2="16"></line>
                        <line x1="20" y1="12" x2="20" y2="3"></line>
                        <line x1="1" y1="14" x2="7" y2="14"></line>
                        <line x1="9" y1="8" x2="15" y2="8"></line>
                        <line x1="17" y1="16" x2="23" y2="16"></line>
                    </svg>
                    <span>Filter</span>
                </button>
                <div class="pk-filter-count" id="catalogCount">
                    Menampilkan katalog barang
                </div>
            </div>

            <!-- Product Grid -->
            <div class="pk-product-grid" id="catalogGrid"></div>

            <!-- Empty State -->
            <div id="catalogEmpty" style="display: none; text-align: center; padding: 48px 20px; background: #FFF; border-radius: 14px; border: 1px dashed #CBD5E1; margin: 20px 0;">
                <div style="font-size: 2.5rem; margin-bottom: 8px;">📦</div>
                <h3 style="font-size: 1.1rem; font-weight: 700; color: #1D356D; margin: 0 0 6px 0;">Tidak ada barang yang sesuai filter</h3>
                <p style="font-size: 0.8rem; color: #64748B; margin: 0 0 16px 0;">Silakan coba kata kunci lain atau reset filter pencarian.</p>
                <button type="button" onclick="resetAllFilters()" class="pk-reset-btn" style="align-self: center;">Reset Semua Filter</button>
            </div>

            <!-- Pagination Bar -->
            <div class="pk-pagination" id="paginationControls"></div>

            <!-- Kondisi Barang Slider Section -->
            <div class="pk-condition-section">
                <div class="pk-condition-title">Kondisi barang</div>
                <div class="pk-condition-slider-wrap">
                    <input type="range" min="0" max="3" value="0" step="1" id="conditionSlider" class="pk-condition-slider" oninput="handleConditionSlider(this.value)">
                </div>
                <div class="pk-condition-labels">
                    <span onclick="setConditionByLabel(0)">Minus</span>
                    <span onclick="setConditionByLabel(1)">Normal</span>
                    <span onclick="setConditionByLabel(2)">Mulus</span>
                    <span onclick="setConditionByLabel(3)">Baru</span>
                </div>
            </div>

        </div>
    </main>

    <!-- ================= 3. FILTER SIDEBAR POP-UP ================= -->
    <div class="pk-sidebar-backdrop" id="filterBackdrop" onclick="closeFilterSidebar()"></div>
    <aside class="pk-sidebar-drawer" id="filterDrawer" aria-label="Filter Barang">
        <div class="pk-sidebar-header">
            <h2 class="pk-sidebar-title">FILTER</h2>
            <button type="button" class="pk-sidebar-close" onclick="closeFilterSidebar()" title="Tutup Filter">✕</button>
        </div>

        <div class="pk-sidebar-body">
            <div>
                <div class="pk-filter-group-title">KATEGORI</div>
                <ul class="pk-filter-list" id="catFilterList">
                    <li class="pk-filter-item" onclick="toggleCategoryFilter('Buku & Akademik', this)">Buku &amp; Akademik</li>
                    <li class="pk-filter-item" onclick="toggleCategoryFilter('Perlengkapan Kos', this)">Perlengkapan Kos</li>
                    <li class="pk-filter-item" onclick="toggleCategoryFilter('Fashion & Apparel', this)">Fashion &amp; Apparel</li>
                    <li class="pk-filter-item" onclick="toggleCategoryFilter('Elektronik', this)">Elektronik</li>
                </ul>
                <a class="pk-see-more-link" onclick="toggleCategoryFilter('all', null)">Lihat semua ▼</a>
            </div>

            <div>
                <div class="pk-filter-group-title">TAGS</div>
                <ul class="pk-filter-list" id="tagFilterList">
                    <li class="pk-filter-item" onclick="toggleTagFilter('#ModulKuliah', this)">#ModulKuliah</li>
                    <li class="pk-filter-item" onclick="toggleTagFilter('#JasAlmamater', this)">#JasAlmamater</li>
                    <li class="pk-filter-item" onclick="toggleTagFilter('#MejaBelajar', this)">#MejaBelajar</li>
                    <li class="pk-filter-item" onclick="toggleTagFilter('#Jaket', this)">#Jaket</li>
                </ul>
                <a class="pk-see-more-link" onclick="toggleTagFilter('all', null)">Lihat semua ▼</a>
            </div>

            <div>
                <div class="pk-filter-group-title">LOKASI</div>
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <label class="pk-checkbox-item">
                        <input type="checkbox" name="lokasi" value="Universitas Brawijaya" onchange="applyFilters()">
                        <span>Area Universitas Brawijaya</span>
                    </label>
                    <label class="pk-checkbox-item">
                        <input type="checkbox" name="lokasi" value="Universitas Negeri Malang" onchange="applyFilters()">
                        <span>Area Universitas Negeri Malang</span>
                    </label>
                    <label class="pk-checkbox-item">
                        <input type="checkbox" name="lokasi" value="Politeknik Negeri Malang" onchange="applyFilters()">
                        <span>Area Politeknik Negeri Malang</span>
                    </label>
                    <label class="pk-checkbox-item">
                        <input type="checkbox" name="lokasi" value="Universitas Indonesia" onchange="applyFilters()">
                        <span>Area Universitas Indonesia</span>
                    </label>
                </div>
                <a class="pk-see-more-link" onclick="selectAllLocations()">Lihat semua ▼</a>
            </div>

            <div>
                <div class="pk-filter-group-title">RENTANG HARGA</div>
                <div class="pk-price-inputs">
                    <div class="pk-price-col">
                        <label for="priceMin">Rp. Min</label>
                        <input type="number" id="priceMin" placeholder="0" oninput="applyFilters()">
                    </div>
                    <div class="pk-price-col">
                        <label for="priceMax">Rp. Max</label>
                        <input type="number" id="priceMax" placeholder="999999" oninput="applyFilters()">
                    </div>
                </div>
            </div>

            <div>
                <button type="button" class="pk-reset-btn" onclick="resetAllFilters()">Reset all</button>
            </div>
        </div>
    </aside>

    <!-- ================= 4. MODAL DETAIL PRODUK ================= -->
    <div id="detailProductModal" class="modal-backdrop">
        <div class="modal-box">
            <div class="modal-header">
                <span id="modalCatBadge" class="badge-tag">Kategori</span>
                <span class="modal-close" onclick="closeProductDetail()">✕</span>
            </div>

            <div style="border-radius: var(--radius-md); overflow: hidden; height: 220px; background: #f1f5f9; margin-bottom: 16px;">
                <img id="modalProductImg" src="" alt="Detail Barang" style="width: 100%; height: 100%; object-fit: cover;">
            </div>

            <div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <span id="modalConditionBadge" class="badge-tag" style="background: #dbeafe; color: #1e40af;">Mulus</span>
                    <div style="text-align: right;">
                        <span id="modalProductOrigPrice" style="font-size: 0.75rem; color: var(--text-light); text-decoration: line-through; display: block;"></span>
                        <div id="modalProductPrice" style="font-size: 1.35rem; font-weight: 800; color: var(--primary);">Rp 0</div>
                    </div>
                </div>

                <h3 id="modalProductTitle" style="font-size: 1.15rem; font-weight: 800; color: var(--secondary); margin-bottom: 6px;">Judul Barang</h3>
                <p id="modalProductDesc" style="font-size: 0.8rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 16px;">Deskripsi lengkap...</p>
            </div>

            <div style="background: var(--bg-main); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 12px 16px; display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="width: 38px; height: 38px; border-radius: 50%; background: #dbeafe; color: #1e40af; font-weight: 800; display: flex; align-items: center; justify-content: center; font-size: 0.85rem;">
                        👤
                    </div>
                    <div>
                        <h4 id="modalSellerName" style="font-size: 0.85rem; font-weight: 700; color: var(--secondary);">Nama Penjual</h4>
                        <p id="modalSellerCampus" style="font-size: 0.7rem; color: var(--text-muted);">Kampus</p>
                    </div>
                </div>
                <span class="badge-tag" style="font-size: 0.65rem;">Terverifikasi</span>
            </div>

            <div style="display: flex; gap: 10px;">
                <button onclick="alert('Barang disimpan ke wishlist favorit Anda!')" class="btn btn-outline" style="padding: 10px 14px;">
                    ❤️
                </button>
                <a id="modalWhatsAppBtn" href="#" target="_blank" class="btn btn-primary" style="flex: 1; padding: 12px; font-weight: 700;">
                    💬 Hubungi Penjual (Chat WhatsApp)
                </a>
            </div>
        </div>
    </div>

    <!-- ================= 5. FOOTER ================= -->
    <footer class="lp-footer" style="margin-top: 48px;">
        <div class="lp-container">
            <div class="lp-footer-grid">
                <div class="lp-footer-col">
                    <a href="<?= route('/') ?>" class="lp-logo" style="color: #FFFFFF;">PasarKampus</a>
                    <p>Platform marketplace eksklusif mahasiswa untuk sirkulasi barang perkuliahan yang aman, hemat, dan berkelanjutan.</p>
                </div>
                <div class="lp-footer-col">
                    <h4>PasarKampus</h4>
                    <ul class="lp-footer-links">
                        <li><a href="<?= route('/#tentang-kami') ?>">Tentang kami</a></li>
                        <li><a href="<?= route('/dashboard.php') ?>">Katalog Barang</a></li>
                        <li><a href="<?= route('/register.php') ?>">Daftar Akun Mahasiswa</a></li>
                        <li><a href="<?= route('/dashboard-penjual.php') ?>">Dashboard Penjual</a></li>
                    </ul>
                </div>
                <div class="lp-footer-col">
                    <h4>Bantuan</h4>
                    <ul class="lp-footer-links">
                        <li><a href="<?= route('/#faq') ?>">Pusat Bantuan</a></li>
                        <li><a href="<?= route('/#faq') ?>">FAQ</a></li>
                        <li><a href="mailto:support@pasarkampus.id">Kontak Kami</a></li>
                    </ul>
                </div>
                <div class="lp-footer-col">
                    <h4>Dukungan</h4>
                    <ul class="lp-footer-links">
                        <li><a href="#" onclick="alert('Kebijakan Privasi: Data kampus dan identitas mahasiswa dilindungi.'); return false;">Kebijakan</a></li>
                        <li><a href="#" onclick="alert('Panduan COD: Selalu lakukan serah terima di area kampus.'); return false;">Panduan COD</a></li>
                        <li><a href="#" onclick="alert('Syarat & Ketentuan berlaku.'); return false;">Syarat &amp; Ketentuan</a></li>
                    </ul>
                </div>
            </div>
            <div class="lp-footer-bottom">
                <div>&copy; <?= date('Y') ?> PasarKampus. Hak Cipta Dilindungi.</div>
                <div>Dibuat untuk Project Based Learning (PBL) Mahasiswa.</div>
            </div>
        </div>
    </footer>

    <script>
        function toggleDashDropdown(e) {
            if (e) e.stopPropagation();
            const d = document.getElementById('dashDropdown');
            if (d) d.style.display = (d.style.display === 'block') ? 'none' : 'block';
        }

        document.addEventListener('click', function(e) {
            const d = document.getElementById('dashDropdown');
            if (d && d.style.display === 'block') {
                d.style.display = 'none';
            }
        });
    </script>
    <script src="<?= route('/js/dashboard.js') ?>"></script>
</body>
</html>

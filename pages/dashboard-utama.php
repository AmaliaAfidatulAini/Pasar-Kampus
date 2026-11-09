<?php
$session = get_session();
$isLoggedIn = !empty($session);
$userName = $session['name'] ?? 'Pengguna';
$allProducts = get_catalog_products();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PasarKampus - Katalog & Dashboard Utama</title>
    <!-- Google Fonts Plus Jakarta Sans & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --pk-blue: #2355B8;
            --pk-blue-hover: #1b4598;
            --pk-navy: #1D356D;
            --pk-navy-dark: #15264F;
            --pk-footer: #1B3260;
            --pk-amber: #F59E0B;
            --pk-amber-dark: #D97706;
            --pk-primary: #2563EB;
            --pk-border: #E2E8F0;
            --pk-bg: #F8FAFC;
            --pk-text: #1E293B;
            --pk-muted: #64748B;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body.pk-body {
            background-color: var(--pk-bg);
            font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            color: var(--pk-text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        /* 1. TOP NAVBAR */
        .pk-navbar {
            background-color: var(--pk-blue);
            height: 64px;
            display: flex;
            align-items: center;
            padding: 0 32px;
            color: #FFFFFF;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }

        .pk-nav-inner {
            width: 100%;
            max-width: 1240px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .pk-logo {
            font-size: 1.35rem;
            font-weight: 800;
            color: #FFFFFF;
            letter-spacing: -0.3px;
            flex-shrink: 0;
        }

        .pk-nav-search {
            flex: 1;
            max-width: 440px;
            position: relative;
        }

        .pk-nav-search input {
            width: 100%;
            height: 38px;
            border-radius: 9999px;
            border: none;
            padding: 0 18px 0 40px;
            font-size: 0.85rem;
            outline: none;
            background: #FFFFFF;
            color: var(--pk-text);
            font-family: inherit;
        }

        .pk-nav-search input::placeholder {
            color: #94A3B8;
        }

        .pk-nav-search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748B;
            pointer-events: none;
            display: flex;
            align-items: center;
        }

        .pk-nav-links {
            display: flex;
            align-items: center;
            gap: 24px;
        }

        .pk-nav-link {
            color: #FFFFFF;
            font-size: 0.92rem;
            font-weight: 600;
            transition: opacity 0.2s;
        }

        .pk-nav-link:hover {
            opacity: 0.85;
        }

        /* 2. MAIN CONTAINER */
        .pk-main-wrapper {
            flex: 1;
            padding: 24px 20px 48px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .pk-content-container {
            width: 100%;
            max-width: 1240px;
        }

        /* 3. HERO BANNER */
        .pk-hero-banner {
            background-color: #FFFFFF;
            border: 1px solid var(--pk-border);
            border-radius: 20px;
            padding: 36px 40px;
            display: grid;
            grid-template-columns: 1.35fr 1fr;
            align-items: center;
            gap: 36px;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.03);
            margin-bottom: 28px;
        }

        .pk-hero-left h1 {
            font-size: 2.2rem;
            font-weight: 800;
            line-height: 1.25;
            color: #0F172A;
            margin-bottom: 12px;
            letter-spacing: -0.5px;
        }

        .pk-hero-highlight {
            color: var(--pk-amber);
        }

        .pk-hero-desc {
            font-size: 0.88rem;
            color: var(--pk-muted);
            line-height: 1.6;
            margin-bottom: 24px;
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
            background: #EFF6FF;
            border: 1px solid #BFDBFE;
            color: var(--pk-primary);
            font-size: 0.78rem;
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 9999px;
        }

        /* HERO RIGHT: FEATURED CARD */
        .pk-hero-right {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }

        .pk-featured-tag {
            border: 1.5px solid var(--pk-amber);
            color: var(--pk-amber-dark);
            font-size: 0.76rem;
            font-weight: 800;
            padding: 4px 14px;
            border-radius: 8px;
            margin-bottom: 10px;
            background: #FFFBEB;
            letter-spacing: 0.2px;
        }

        .pk-featured-card {
            background: #FFFFFF;
            border: 1px solid var(--pk-border);
            border-radius: 14px;
            padding: 12px;
            width: 100%;
            max-width: 280px;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.04);
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .pk-featured-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
        }

        /* 4. FILTER TOOLBAR */
        .pk-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 0 0 20px;
        }

        .pk-filter-btn {
            background: transparent;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--pk-text);
            padding: 6px 12px 6px 0;
            border-radius: 8px;
            font-family: inherit;
            transition: color 0.2s;
        }

        .pk-filter-btn:hover {
            color: var(--pk-primary);
        }

        .pk-toolbar-count {
            font-size: 0.84rem;
            font-weight: 600;
            color: #475569;
        }

        /* 5. PRODUCT GRID (3 COLUMNS DESKTOP) */
        .pk-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 36px;
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
            position: relative;
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
            margin-bottom: 10px;
        }

        .pk-badge-condition {
            background-color: var(--pk-text);
            color: #FFFFFF;
            font-size: 0.72rem;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 6px;
        }

        .pk-badge-category {
            background-color: #EFF6FF;
            border: 1px solid #BFDBFE;
            color: var(--pk-primary);
            font-size: 0.72rem;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 6px;
        }

        .pk-card-img-wrap {
            height: 180px;
            border-radius: 8px;
            overflow: hidden;
            background: #F1F5F9;
            margin-bottom: 10px;
        }

        .pk-card-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.3s ease;
        }

        .pk-card:hover .pk-card-img-wrap img {
            transform: scale(1.03);
        }

        .pk-card-location {
            font-size: 0.74rem;
            color: #64748B;
            display: flex;
            align-items: center;
            gap: 4px;
            margin-bottom: 6px;
        }

        .pk-card-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--pk-text);
            margin-bottom: 6px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .pk-card-desc {
            font-size: 0.76rem;
            color: var(--pk-muted);
            line-height: 1.45;
            margin-bottom: 10px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            height: 34px;
        }

        .pk-card-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 12px;
        }

        .pk-tag-pill {
            background: #FEF3C7;
            color: #D97706;
            font-size: 0.68rem;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 4px;
        }

        .pk-card-orig-price {
            font-size: 0.74rem;
            color: #94A3B8;
            text-decoration: line-through;
            margin-bottom: 2px;
        }

        .pk-card-price {
            font-size: 1.05rem;
            font-weight: 800;
            color: var(--pk-primary);
        }

        /* 6. PAGINATION */
        .pk-pagination {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin: 20px 0 10px;
        }

        .pk-page-btn {
            min-width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--pk-text);
            cursor: pointer;
            transition: all 0.15s;
            background: transparent;
            border: none;
            padding: 0 6px;
            font-family: inherit;
        }

        .pk-page-btn:hover {
            background: #E2E8F0;
        }

        .pk-page-btn.active {
            background: var(--pk-primary);
            color: #FFFFFF;
            border-radius: 9999px;
            width: 34px;
            height: 34px;
        }

        /* 7. FILTER SIDEBAR (DRAWER POP-UP) */
        .pk-sidebar-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.45);
            backdrop-filter: blur(2px);
            z-index: 998;
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
            z-index: 999;
            box-shadow: 8px 0 30px rgba(15, 23, 42, 0.15);
            transform: translateX(-100%);
            transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1);
            display: flex;
            flex-direction: column;
        }

        .pk-sidebar-drawer.active {
            transform: translateX(0);
        }

        /* SIDEBAR HEADER */
        .pk-sidebar-header {
            background-color: var(--pk-blue);
            height: 60px;
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: #FFFFFF;
            flex-shrink: 0;
        }

        .pk-sidebar-title {
            font-size: 1.1rem;
            font-weight: 800;
            color: #FBBF24; /* Yellow title */
            letter-spacing: 0.5px;
        }

        .pk-sidebar-close {
            background: none;
            border: none;
            font-size: 1.4rem;
            color: #FBBF24; /* Yellow close icon */
            cursor: pointer;
            padding: 4px 8px;
            line-height: 1;
            transition: opacity 0.15s;
        }

        .pk-sidebar-close:hover {
            opacity: 0.8;
        }

        /* SIDEBAR BODY */
        .pk-sidebar-body {
            flex: 1;
            overflow-y: auto;
            padding: 24px 22px;
            display: flex;
            flex-direction: column;
            gap: 22px;
        }

        .pk-filter-group-title {
            font-size: 0.85rem;
            font-weight: 800;
            color: var(--pk-navy);
            letter-spacing: 0.3px;
            margin-bottom: 12px;
            text-transform: uppercase;
        }

        .pk-filter-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .pk-filter-item {
            font-size: 0.84rem;
            font-weight: 500;
            color: #334155;
            cursor: pointer;
            padding: 4px 6px;
            border-radius: 6px;
            transition: all 0.15s;
            user-select: none;
        }

        .pk-filter-item:hover {
            color: var(--pk-primary);
            background: #F1F5F9;
        }

        .pk-filter-item.active {
            color: var(--pk-primary);
            font-weight: 700;
            background: #EFF6FF;
        }

        .pk-see-more {
            font-size: 0.78rem;
            color: #334155;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            margin-top: 6px;
            user-select: none;
            font-weight: 600;
            transition: color 0.15s;
        }

        .pk-see-more:hover {
            color: var(--pk-primary);
        }

        .pk-checkbox-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .pk-checkbox-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.84rem;
            color: #334155;
            cursor: pointer;
            user-select: none;
        }

        .pk-checkbox-item input[type="checkbox"] {
            width: 17px;
            height: 17px;
            border: 1.5px solid #CBD5E1;
            border-radius: 4px;
            cursor: pointer;
            accent-color: var(--pk-navy);
        }

        .pk-price-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .pk-price-box label {
            display: block;
            font-size: 0.75rem;
            font-weight: 700;
            color: #334155;
            margin-bottom: 5px;
        }

        .pk-price-box input {
            width: 100%;
            height: 36px;
            border: 1px solid #CBD5E1;
            border-radius: 6px;
            padding: 0 10px;
            font-size: 0.82rem;
            outline: none;
            box-sizing: border-box;
            font-family: inherit;
        }

        .pk-price-box input:focus {
            border-color: var(--pk-primary);
        }

        .pk-reset-btn {
            background-color: var(--pk-navy);
            color: #FFFFFF;
            border: none;
            border-radius: 8px;
            padding: 10px 22px;
            font-size: 0.84rem;
            font-weight: 700;
            cursor: pointer;
            align-self: flex-start;
            font-family: inherit;
            transition: background-color 0.2s;
        }

        .pk-reset-btn:hover {
            background-color: var(--pk-navy-dark);
        }

        /* 8. FOOTER */
        .pk-footer {
            background-color: var(--pk-footer);
            color: #CBD5E1;
            padding: 48px 24px 24px;
            margin-top: auto;
        }

        .pk-footer-inner {
            max-width: 1240px;
            margin: 0 auto;
        }

        .pk-footer-grid {
            display: grid;
            grid-template-columns: 1.6fr 1fr 1fr 1fr;
            gap: 36px;
            margin-bottom: 36px;
        }

        .pk-footer-col h4 {
            color: #FFFFFF;
            font-size: 0.95rem;
            font-weight: 700;
            margin-bottom: 14px;
        }

        .pk-footer-col p {
            font-size: 0.84rem;
            line-height: 1.6;
            color: #94A3B8;
            max-width: 320px;
        }

        .pk-footer-links {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .pk-footer-links a {
            font-size: 0.84rem;
            color: #CBD5E1;
            transition: color 0.15s;
        }

        .pk-footer-links a:hover {
            color: #FFFFFF;
        }

        .pk-footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.78rem;
            color: #94A3B8;
            flex-wrap: wrap;
            gap: 12px;
        }

        /* RESPONSIVE */
        @media (max-width: 1024px) {
            .pk-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .pk-hero-banner {
                grid-template-columns: 1fr;
                gap: 24px;
            }
            .pk-hero-right {
                align-items: flex-start;
            }
            .pk-footer-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 640px) {
            .pk-grid {
                grid-template-columns: 1fr;
            }
            .pk-navbar {
                padding: 0 16px;
            }
            .pk-hero-banner {
                padding: 24px 20px;
            }
            .pk-hero-left h1 {
                font-size: 1.7rem;
            }
            .pk-footer-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body class="pk-body">

    <!-- ================= 1. TOP NAVBAR ================= -->
    <header class="pk-navbar">
        <div class="pk-nav-inner">
            <a href="<?= route('/dashboard-utama.php') ?>" class="pk-logo">
                PasarKampus
            </a>

            <div class="pk-nav-search">
                <span class="pk-nav-search-icon">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </span>
                <input type="text" id="dashSearch" oninput="applyCatalogFilters()" placeholder="Cari barang....">
            </div>

            <nav class="pk-nav-links">
                <a href="<?= route('/riwayat.php') ?>" class="pk-nav-link">Riwayat</a>
                <a href="<?= route('/profil.php') ?>" class="pk-nav-link">Profil</a>
            </nav>
        </div>
    </header>

    <!-- ================= 2. MAIN DASHBOARD CONTENT ================= -->
    <main class="pk-main-wrapper">
        <div class="pk-content-container">

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
                        <div class="pk-pill-badge">🎓 100% Khusus Mahasiswa</div>
                        <div class="pk-pill-badge">🤝 Aman COD Area Kampus</div>
                        <div class="pk-pill-badge">🏷️ Harga terjangkau</div>
                    </div>
                </div>

                <div class="pk-hero-right">
                    <span class="pk-featured-tag">Baru Diunggah</span>
                    <div class="pk-featured-card" onclick="goToProductDetail(1)">
                        <div class="pk-card-top-badges">
                            <span class="pk-badge-condition">Layak pakai</span>
                            <span class="pk-badge-category">Elektronik</span>
                        </div>
                        <div class="pk-card-img-wrap" style="height: 130px;">
                            <img src="https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&q=80&w=800" alt="Keyboard Logiac - Silver">
                        </div>
                        <span class="pk-card-location">📍 Kecamatan Lowokwaru</span>
                        <h3 class="pk-card-title">Keyboard Logiac - Silver</h3>
                        <p class="pk-card-desc">Keyboard wireless dengan balutan warna silver elegan dan desain slim yang hemat tempat. ..</p>
                        <div class="pk-card-orig-price">Rp. 150.000</div>
                        <div class="pk-card-price">Rp. 90.000</div>
                    </div>
                </div>
            </section>

            <!-- Toolbar: Filter Button & Count -->
            <div class="pk-toolbar">
                <button type="button" class="pk-filter-btn" onclick="openSidebar()" title="Buka Filter">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
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
                <div class="pk-toolbar-count" id="itemCountDisplay">
                    Menampilkan 1-20 dari 66 barang
                </div>
            </div>

            <!-- Product Grid (3 Columns) -->
            <div class="pk-grid" id="productGrid">
                <!-- Javascript will render or fallback to initial PHP render -->
                <?php foreach (array_slice($allProducts, 0, 12) as $item): ?>
                    <div class="pk-card" onclick="goToProductDetail(<?= $item['id'] ?>)">
                        <div class="pk-card-top-badges">
                            <span class="pk-badge-condition"><?= esc($item['condition']) ?></span>
                            <span class="pk-badge-category"><?= esc($item['category']) ?></span>
                        </div>
                        <div class="pk-card-img-wrap">
                            <img src="<?= esc($item['image']) ?>" alt="<?= esc($item['title']) ?>" loading="lazy">
                        </div>
                        <span class="pk-card-location">📍 <?= esc($item['location']) ?></span>
                        <h3 class="pk-card-title"><?= esc($item['title']) ?></h3>
                        <p class="pk-card-desc"><?= esc($item['short_desc']) ?></p>
                        <div class="pk-card-tags">
                            <?php foreach ($item['tags'] as $tag): ?>
                                <span class="pk-tag-pill"><?= esc($tag) ?></span>
                            <?php endforeach; ?>
                        </div>
                        <div class="pk-card-orig-price"><?= format_rupiah($item['original_price']) ?></div>
                        <div class="pk-card-price"><?= format_rupiah($item['price']) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Empty State -->
            <div id="gridEmpty" style="display: none; text-align: center; padding: 48px 20px; background: #FFF; border-radius: 14px; border: 1px dashed var(--pk-border); margin: 20px 0;">
                <div style="font-size: 2.5rem; margin-bottom: 8px;">📦</div>
                <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--pk-navy); margin: 0 0 6px 0;">Tidak ada barang yang cocok</h3>
                <p style="font-size: 0.84rem; color: #64748B; margin: 0 0 16px 0;">Coba atur ulang filter pencarian atau gunakan kata kunci lain.</p>
                <button type="button" onclick="resetSidebarFilters()" class="pk-reset-btn" style="align-self: center;">Reset Semua Filter</button>
            </div>

            <!-- Centered Pagination -->
            <div class="pk-pagination" id="paginationBar">
                <button type="button" class="pk-page-btn" onclick="changePage('prev')">&lt;</button>
                <button type="button" class="pk-page-btn active" onclick="setPage(1)">1</button>
                <button type="button" class="pk-page-btn" onclick="setPage(2)">2</button>
                <button type="button" class="pk-page-btn" onclick="setPage(3)">3</button>
                <button type="button" class="pk-page-btn" onclick="setPage(4)">4</button>
                <button type="button" class="pk-page-btn" onclick="setPage(5)">5</button>
                <button type="button" class="pk-page-btn" onclick="changePage('next')">&gt;</button>
            </div>

        </div>
    </main>

    <!-- ================= 3. FILTER SIDEBAR (DRAWER) ================= -->
    <div class="pk-sidebar-backdrop" id="sidebarBackdrop" onclick="closeSidebar()"></div>
    <aside class="pk-sidebar-drawer" id="sidebarDrawer" aria-label="Filter Barang">
        <!-- Sidebar Header: Blue with Yellow FILTER and Close -->
        <div class="pk-sidebar-header">
            <span class="pk-sidebar-title">FILTER</span>
            <button type="button" class="pk-sidebar-close" onclick="closeSidebar()" title="Tutup Filter">&times;</button>
        </div>

        <div class="pk-sidebar-body">
            <!-- 1. KATEGORI -->
            <div>
                <div class="pk-filter-group-title">KATEGORI</div>
                <ul class="pk-filter-list" id="sidebarCatList">
                    <li class="pk-filter-item" onclick="selectCategory('Buku & Akademik', this)">Buku &amp; Akademik</li>
                    <li class="pk-filter-item" onclick="selectCategory('Perlengkapan Kos', this)">Perlengkapan Kos</li>
                    <li class="pk-filter-item" onclick="selectCategory('Fashion & Apparel', this)">Fashion &amp; Apparel</li>
                    <li class="pk-filter-item" onclick="selectCategory('Elektronik', this)">Elektronik</li>
                </ul>
                <div class="pk-see-more" onclick="toggleExpandList('sidebarCatList', this)">Lihat semua &#x25BE;</div>
            </div>

            <!-- 2. TAGS -->
            <div>
                <div class="pk-filter-group-title">TAGS</div>
                <ul class="pk-filter-list" id="sidebarTagList">
                    <li class="pk-filter-item" onclick="selectTag('#ModulKuliah', this)">#ModulKuliah</li>
                    <li class="pk-filter-item" onclick="selectTag('#JasAlmamater', this)">#JasAlmamater</li>
                    <li class="pk-filter-item" onclick="selectTag('#MejaBelajar', this)">#MejaBelajar</li>
                    <li class="pk-filter-item" onclick="selectTag('#Jaket', this)">#Jaket</li>
                </ul>
                <div class="pk-see-more" onclick="toggleExpandList('sidebarTagList', this)">Lihat semua &#x25BE;</div>
            </div>

            <!-- 3. LOKASI -->
            <div>
                <div class="pk-filter-group-title">LOKASI</div>
                <div class="pk-checkbox-group" id="sidebarLocGroup">
                    <label class="pk-checkbox-item">
                        <input type="checkbox" name="locFilter" value="Area Universitas Brawijaya" onchange="applyCatalogFilters()">
                        <span>Area Universitas Brawijaya</span>
                    </label>
                    <label class="pk-checkbox-item">
                        <input type="checkbox" name="locFilter" value="Area Universitas Negeri Malang" onchange="applyCatalogFilters()">
                        <span>Area Universitas Negeri Malang</span>
                    </label>
                    <label class="pk-checkbox-item">
                        <input type="checkbox" name="locFilter" value="Area Politeknik Negeri Malang" onchange="applyCatalogFilters()">
                        <span>Area Politeknik Negeri Malang</span>
                    </label>
                    <label class="pk-checkbox-item">
                        <input type="checkbox" name="locFilter" value="Area Universitas Terbuka" onchange="applyCatalogFilters()">
                        <span>Area Universitas Terbuka</span>
                    </label>
                </div>
                <div class="pk-see-more" onclick="toggleExpandList('sidebarLocGroup', this)">Lihat semua &#x25BE;</div>
            </div>

            <!-- 4. RENTANG HARGA -->
            <div>
                <div class="pk-filter-group-title">RENTANG HARGA</div>
                <div class="pk-price-row">
                    <div class="pk-price-box">
                        <label for="priceMinInput">Rp. Min</label>
                        <input type="number" id="priceMinInput" placeholder="" oninput="applyCatalogFilters()">
                    </div>
                    <div class="pk-price-box">
                        <label for="priceMaxInput">Rp. Max</label>
                        <input type="number" id="priceMaxInput" placeholder="" oninput="applyCatalogFilters()">
                    </div>
                </div>
            </div>

            <!-- 5. RESET ALL BUTTON -->
            <div>
                <button type="button" class="pk-reset-btn" onclick="resetSidebarFilters()">Reset all</button>
            </div>
        </div>
    </aside>

    <!-- ================= 4. FOOTER ================= -->
    <footer class="pk-footer">
        <div class="pk-footer-inner">
            <div class="pk-footer-grid">
                <div class="pk-footer-col">
                    <h3 style="color: #FFFFFF; font-size: 1.35rem; font-weight: 800; margin-bottom: 12px;">PasarKampus</h3>
                    <p>Platform marketplace eksklusif mahasiswa untuk sirkulasi barang perkuliahan yang aman, hemat, dan berkelanjutan.</p>
                </div>
                <div class="pk-footer-col">
                    <h4>PasarKampus</h4>
                    <ul class="pk-footer-links">
                        <li><a href="<?= route('/') ?>">Tentang kami</a></li>
                        <li><a href="<?= route('/dashboard-utama.php') ?>">Katalog Barang</a></li>
                        <li><a href="<?= route('/register.php') ?>">Daftar Akun Mahasiswa</a></li>
                        <li><a href="<?= route('/dashboard-penjual.php') ?>">Dashboard Penjual</a></li>
                    </ul>
                </div>
                <div class="pk-footer-col">
                    <h4>Bantuan</h4>
                    <ul class="pk-footer-links">
                        <li><a href="#">Pusat Bantuan</a></li>
                        <li><a href="#">FAQ</a></li>
                        <li><a href="mailto:support@pasarkampus.id">Kontak Kami</a></li>
                    </ul>
                </div>
                <div class="pk-footer-col">
                    <h4>Dukungan</h4>
                    <ul class="pk-footer-links">
                        <li><a href="#">Kebijakan</a></li>
                        <li><a href="#">Panduan Keamanan COD</a></li>
                        <li><a href="#">Syarat &amp; Ketentuan</a></li>
                    </ul>
                </div>
            </div>
            <div class="pk-footer-bottom">
                <div>&copy; <?= date('Y') ?> PasarKampus, Hak Cipta Dilindungi.</div>
                <div>Dibuat untuk Project Based Learning (PBL) Mahasiswa</div>
            </div>
        </div>
    </footer>

    <!-- ================= JAVASCRIPT LOGIC ================= -->
    <script>
        // Master product catalog passed from PHP
        const rawProducts = <?= json_encode($allProducts, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
        
        let selectedCategory = '';
        let selectedTag = '';
        let currentPage = 1;
        const pageSize = 12;

        function formatRupiah(num) {
            return 'Rp. ' + Number(num).toLocaleString('id-ID');
        }

        function openSidebar() {
            document.getElementById('sidebarDrawer').classList.add('active');
            document.getElementById('sidebarBackdrop').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            document.getElementById('sidebarDrawer').classList.remove('active');
            document.getElementById('sidebarBackdrop').classList.remove('active');
            document.body.style.overflow = '';
        }

        function selectCategory(cat, element) {
            if (selectedCategory === cat) {
                selectedCategory = '';
                element.classList.remove('active');
            } else {
                selectedCategory = cat;
                document.querySelectorAll('#sidebarCatList .pk-filter-item').forEach(el => el.classList.remove('active'));
                element.classList.add('active');
            }
            applyCatalogFilters();
        }

        function selectTag(tag, element) {
            if (selectedTag === tag) {
                selectedTag = '';
                element.classList.remove('active');
            } else {
                selectedTag = tag;
                document.querySelectorAll('#sidebarTagList .pk-filter-item').forEach(el => el.classList.remove('active'));
                element.classList.add('active');
            }
            applyCatalogFilters();
        }

        function toggleExpandList(listId, toggleBtn) {
            const list = document.getElementById(listId);
            if (!list) return;
            // Visual toggle feedback
            if (toggleBtn.innerText.includes('▼')) {
                toggleBtn.innerText = 'Tutup ▲';
            } else {
                toggleBtn.innerText = 'Lihat semua ▼';
            }
        }

        function resetSidebarFilters() {
            selectedCategory = '';
            selectedTag = '';
            document.querySelectorAll('#sidebarCatList .pk-filter-item').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('#sidebarTagList .pk-filter-item').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('input[name="locFilter"]').forEach(cb => cb.checked = false);
            
            const minEl = document.getElementById('priceMinInput');
            const maxEl = document.getElementById('priceMaxInput');
            if (minEl) minEl.value = '';
            if (maxEl) maxEl.value = '';
            
            const searchEl = document.getElementById('dashSearch');
            if (searchEl) searchEl.value = '';

            currentPage = 1;
            applyCatalogFilters();
        }

        function applyCatalogFilters() {
            const searchVal = (document.getElementById('dashSearch')?.value || '').toLowerCase().trim();
            const minPrice = parseFloat(document.getElementById('priceMinInput')?.value) || 0;
            const maxPrice = parseFloat(document.getElementById('priceMaxInput')?.value) || Infinity;

            const checkedLocations = Array.from(document.querySelectorAll('input[name="locFilter"]:checked'))
                .map(cb => cb.value.toLowerCase());

            const filtered = rawProducts.filter(item => {
                // Category match
                if (selectedCategory && item.category.toLowerCase() !== selectedCategory.toLowerCase()) {
                    return false;
                }

                // Tag match
                if (selectedTag && !item.tags.map(t => t.toLowerCase()).includes(selectedTag.toLowerCase())) {
                    return false;
                }

                // Location match
                if (checkedLocations.length > 0) {
                    const itemLoc = (item.campus || '').toLowerCase();
                    const hasMatch = checkedLocations.some(loc => itemLoc.includes(loc) || loc.includes(itemLoc));
                    if (!hasMatch) return false;
                }

                // Price range match
                if (item.price < minPrice || item.price > maxPrice) {
                    return false;
                }

                // Keyword search match
                if (searchVal) {
                    const inTitle = item.title.toLowerCase().includes(searchVal);
                    const inDesc = (item.description || '').toLowerCase().includes(searchVal);
                    const inLoc = (item.location || '').toLowerCase().includes(searchVal);
                    const inCampus = (item.campus || '').toLowerCase();
                    const inTags = (item.tags || []).some(t => t.toLowerCase().includes(searchVal));
                    if (!inTitle && !inDesc && !inLoc && !inCampus && !inTags) {
                        return false;
                    }
                }

                return true;
            });

            renderGrid(filtered);
        }

        function renderGrid(items) {
            const grid = document.getElementById('productGrid');
            const empty = document.getElementById('gridEmpty');
            const countDisplay = document.getElementById('itemCountDisplay');
            const paginationBar = document.getElementById('paginationBar');

            if (!grid) return;

            if (items.length === 0) {
                grid.innerHTML = '';
                if (empty) empty.style.display = 'block';
                if (countDisplay) countDisplay.innerText = 'Menampilkan 0 barang';
                if (paginationBar) paginationBar.style.display = 'none';
                return;
            }

            if (empty) empty.style.display = 'none';
            if (paginationBar) paginationBar.style.display = 'flex';
            if (countDisplay) {
                countDisplay.innerText = `Menampilkan 1-${Math.min(items.length, 20)} dari ${Math.max(items.length, 66)} barang`;
            }

            const startIdx = (currentPage - 1) * pageSize;
            const pageItems = items.slice(startIdx, startIdx + pageSize);

            grid.innerHTML = pageItems.map(item => `
                <div class="pk-card" onclick="goToProductDetail(${item.id})">
                    <div class="pk-card-top-badges">
                        <span class="pk-badge-condition">${escapeHtml(item.condition || 'Layak pakai')}</span>
                        <span class="pk-badge-category">${escapeHtml(item.category || 'Elektronik')}</span>
                    </div>
                    <div class="pk-card-img-wrap">
                        <img src="${escapeHtml(item.image)}" alt="${escapeHtml(item.title)}" loading="lazy">
                    </div>
                    <span class="pk-card-location">📍 ${escapeHtml(item.location || 'Kecamatan Lowokwaru')}</span>
                    <h3 class="pk-card-title">${escapeHtml(item.title)}</h3>
                    <p class="pk-card-desc">${escapeHtml(item.short_desc || item.description || '')}</p>
                    <div class="pk-card-tags">
                        ${(item.tags || []).map(t => `<span class="pk-tag-pill">${escapeHtml(t)}</span>`).join('')}
                    </div>
                    <div class="pk-card-orig-price">${formatRupiah(item.original_price)}</div>
                    <div class="pk-card-price">${formatRupiah(item.price)}</div>
                </div>
            `).join('');
        }

        function goToProductDetail(id) {
            window.location.href = `<?= route('/barang') ?>?id=${encodeURIComponent(id)}`;
        }

        function setPage(pageNum) {
            currentPage = pageNum;
            document.querySelectorAll('#paginationBar .pk-page-btn').forEach(btn => {
                if (btn.innerText.trim() === String(pageNum)) {
                    btn.classList.add('active');
                } else if (!['<', '>'].includes(btn.innerText.trim())) {
                    btn.classList.remove('active');
                }
            });
            applyCatalogFilters();
            window.scrollTo({ top: 380, behavior: 'smooth' });
        }

        function changePage(direction) {
            if (direction === 'prev' && currentPage > 1) {
                setPage(currentPage - 1);
            } else if (direction === 'next' && currentPage < 5) {
                setPage(currentPage + 1);
            }
        }

        function escapeHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }
    </script>
</body>
</html>

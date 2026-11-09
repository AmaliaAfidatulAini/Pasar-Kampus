<?php
$session = get_session();
$isLoggedIn = !empty($session);
$userName = $session['name'] ?? 'Pengguna';

// If no id passed, redirect to dashboard utama catalog
if (!isset($_GET['id']) || trim((string)$_GET['id']) === '') {
    redirect('/dashboard-utama.php', 302);
    exit;
}

$productId = $_GET['id'];
$product = get_catalog_product_by_id($productId);
$allProducts = get_catalog_products();

// Get 4 recommendation products (excluding current or similar)
$recommendations = [];
foreach ($allProducts as $p) {
    if ((int)$p['id'] !== (int)$product['id']) {
        $recommendations[] = $p;
    }
    if (count($recommendations) === 4) break;
}
// If not enough recommendations, pad with items
if (count($recommendations) < 4) {
    $recommendations = array_slice($allProducts, 0, 4);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($product['title']) ?> - Detail Barang PasarKampus</title>
    <!-- Google Fonts Plus Jakarta Sans & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --pk-blue: #2355B8;
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

        body.pk-detail-body {
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

        /* 2. MAIN LAYOUT */
        .pk-main-wrapper {
            flex: 1;
            padding: 20px 20px 48px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .pk-detail-container {
            width: 100%;
            max-width: 1240px;
        }

        /* BACK BUTTON */
        .pk-back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.95rem;
            font-weight: 700;
            color: #1E293B;
            margin-bottom: 16px;
            transition: color 0.15s;
        }

        .pk-back-btn:hover {
            color: var(--pk-primary);
        }

        /* 3. PRODUCT DETAIL CARD */
        .pk-detail-card {
            background: #FFFFFF;
            border: 1px solid var(--pk-border);
            border-radius: 18px;
            padding: 36px 40px;
            display: grid;
            grid-template-columns: 420px 1fr;
            gap: 48px;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.03);
            margin-bottom: 40px;
        }

        /* LEFT GALLERY */
        .pk-gallery-col {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .pk-main-img-box {
            width: 100%;
            height: 380px;
            border-radius: 12px;
            overflow: hidden;
            background: #F1F5F9;
            border: 1px solid var(--pk-border);
        }

        .pk-main-img-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: opacity 0.2s;
        }

        .pk-thumb-carousel {
            display: flex;
            align-items: center;
            gap: 10px;
            justify-content: space-between;
        }

        .pk-carousel-nav {
            background: none;
            border: none;
            font-size: 1.1rem;
            color: #64748B;
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.15s;
        }

        .pk-carousel-nav:hover {
            color: var(--pk-navy);
        }

        .pk-thumb-list {
            display: flex;
            gap: 10px;
            overflow-x: auto;
            flex: 1;
            justify-content: space-between;
        }

        .pk-thumb-item {
            width: 58px;
            height: 58px;
            border-radius: 8px;
            overflow: hidden;
            border: 2px solid transparent;
            cursor: pointer;
            background: #F1F5F9;
            flex-shrink: 0;
            transition: border-color 0.15s, transform 0.15s;
        }

        .pk-thumb-item:hover {
            transform: scale(1.04);
        }

        .pk-thumb-item.active {
            border-color: #B45309; /* Amber brown active border */
        }

        .pk-thumb-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        /* RIGHT INFO */
        .pk-info-col {
            display: flex;
            flex-direction: column;
        }

        .pk-detail-title {
            font-size: 1.85rem;
            font-weight: 800;
            color: var(--pk-navy);
            margin-bottom: 20px;
            line-height: 1.25;
            letter-spacing: -0.5px;
        }

        .pk-section-heading {
            font-size: 1.05rem;
            font-weight: 800;
            color: var(--pk-navy);
            margin-bottom: 12px;
        }

        /* SPECIFICATION TABLE */
        .pk-spec-table {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 24px;
            font-size: 0.88rem;
        }

        .pk-spec-row {
            display: flex;
            align-items: center;
        }

        .pk-spec-label {
            width: 120px;
            color: #334155;
            font-weight: 500;
            flex-shrink: 0;
        }

        .pk-spec-colon {
            margin-right: 14px;
            color: #334155;
            font-weight: 500;
        }

        .pk-spec-value {
            color: #0F172A;
            font-weight: 500;
        }

        /* DESCRIPTION */
        .pk-detail-desc {
            font-size: 0.88rem;
            color: #475569;
            line-height: 1.65;
            margin-bottom: 28px;
            max-width: 620px;
        }

        /* ACTION BUTTONS */
        .pk-detail-actions {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-top: auto;
        }

        .pk-btn-report {
            background-color: #FFFFFF;
            border: 1.5px solid #FCA5A5;
            color: #DC2626;
            font-size: 0.88rem;
            font-weight: 700;
            padding: 10px 24px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.15s;
            font-family: inherit;
        }

        .pk-btn-report:hover {
            background-color: #FEF2F2;
            border-color: #EF4444;
        }

        .pk-btn-buy {
            background-color: var(--pk-navy);
            border: none;
            color: #FFFFFF;
            font-size: 0.88rem;
            font-weight: 700;
            padding: 11px 28px;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.2s;
            font-family: inherit;
        }

        .pk-btn-buy:hover {
            background-color: var(--pk-navy-dark);
        }

        /* 4. RECOMMENDATION SECTION */
        .pk-rec-section {
            margin-top: 10px;
        }

        .pk-rec-title {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--pk-navy);
            margin-bottom: 20px;
        }

        .pk-rec-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
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
            background-color: var(--pk-text);
            color: #FFFFFF;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 6px;
        }

        .pk-badge-category {
            background-color: #EFF6FF;
            border: 1px solid #BFDBFE;
            color: var(--pk-primary);
            font-size: 0.7rem;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 6px;
        }

        .pk-card-img-wrap {
            height: 150px;
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
            font-size: 0.72rem;
            color: #64748B;
            display: flex;
            align-items: center;
            gap: 4px;
            margin-bottom: 4px;
        }

        .pk-card-title {
            font-size: 0.92rem;
            font-weight: 700;
            color: var(--pk-text);
            margin-bottom: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .pk-card-desc {
            font-size: 0.74rem;
            color: var(--pk-muted);
            line-height: 1.4;
            margin-bottom: 10px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            height: 32px;
        }

        .pk-card-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            margin-bottom: 10px;
        }

        .pk-tag-pill {
            background: #FEF3C7;
            color: #D97706;
            font-size: 0.65rem;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 4px;
        }

        .pk-card-orig-price {
            font-size: 0.72rem;
            color: #94A3B8;
            text-decoration: line-through;
            margin-bottom: 2px;
        }

        .pk-card-price {
            font-size: 1rem;
            font-weight: 800;
            color: var(--pk-primary);
        }

        /* 5. FOOTER */
        .pk-footer {
            background-color: var(--pk-footer);
            color: #CBD5E1;
            padding: 48px 24px 24px;
            margin-top: 56px;
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

        /* MODAL POPUP FOR AJUKAN / LAPORKAN */
        .pk-modal-backdrop {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.5);
            backdrop-filter: blur(3px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .pk-modal-backdrop.show {
            display: flex;
        }

        .pk-modal-box {
            background: #FFFFFF;
            border-radius: 16px;
            padding: 28px;
            max-width: 440px;
            width: 90%;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            text-align: center;
        }

        /* RESPONSIVE */
        @media (max-width: 960px) {
            .pk-detail-card {
                grid-template-columns: 1fr;
                gap: 28px;
                padding: 24px;
            }
            .pk-rec-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .pk-footer-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 540px) {
            .pk-rec-grid {
                grid-template-columns: 1fr;
            }
            .pk-footer-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body class="pk-detail-body">

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
                <input type="text" placeholder="Cari barang...." onkeydown="if(event.key==='Enter') window.location.href='<?= route('/dashboard-utama.php') ?>'">
            </div>

            <nav class="pk-nav-links">
                <a href="<?= route('/riwayat.php') ?>" class="pk-nav-link">Riwayat</a>
                <a href="<?= route('/profil.php') ?>" class="pk-nav-link">Profil</a>
            </nav>
        </div>
    </header>

    <!-- ================= 2. DETAIL PRODUCT SECTION ================= -->
    <main class="pk-main-wrapper">
        <div class="pk-detail-container">

            <!-- Back Link -->
            <a href="<?= route('/dashboard-utama.php') ?>" class="pk-back-btn">
                <span>&larr;</span> Kembali
            </a>

            <!-- Main Product Card -->
            <div class="pk-detail-card">

                <!-- Left Column: Image & Thumbnails Carousel -->
                <div class="pk-gallery-col">
                    <div class="pk-main-img-box">
                        <img id="mainProductImage" src="<?= esc($product['image']) ?>" alt="<?= esc($product['title']) ?>">
                    </div>

                    <!-- Thumbnails with < and > arrows -->
                    <div class="pk-thumb-carousel">
                        <button type="button" class="pk-carousel-nav" onclick="prevThumb()">&lt;</button>
                        <div class="pk-thumb-list" id="thumbList">
                            <?php foreach ($product['gallery'] as $index => $imgUrl): ?>
                                <div class="pk-thumb-item <?= $index === 0 ? 'active' : '' ?>" onclick="switchThumb('<?= esc($imgUrl) ?>', this)">
                                    <img src="<?= esc($imgUrl) ?>" alt="Thumbnail <?= $index + 1 ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="pk-carousel-nav" onclick="nextThumb()">&gt;</button>
                    </div>
                </div>

                <!-- Right Column: Product Specifications & Description -->
                <div class="pk-info-col">
                    <h1 class="pk-detail-title"><?= esc($product['title']) ?></h1>

                    <div class="pk-section-heading">Spesifikasi Produk</div>
                    <div class="pk-spec-table">
                        <div class="pk-spec-row">
                            <span class="pk-spec-label">Harga asli</span>
                            <span class="pk-spec-colon">:</span>
                            <span class="pk-spec-value"><?= format_rupiah($product['original_price']) ?></span>
                        </div>
                        <div class="pk-spec-row">
                            <span class="pk-spec-label">Harga jual</span>
                            <span class="pk-spec-colon">:</span>
                            <span class="pk-spec-value"><?= format_rupiah($product['price']) ?></span>
                        </div>
                        <div class="pk-spec-row">
                            <span class="pk-spec-label">Kondisi</span>
                            <span class="pk-spec-colon">:</span>
                            <span class="pk-spec-value"><?= esc($product['condition_spec']) ?></span>
                        </div>
                        <div class="pk-spec-row">
                            <span class="pk-spec-label">Metode</span>
                            <span class="pk-spec-colon">:</span>
                            <span class="pk-spec-value"><?= esc($product['method']) ?></span>
                        </div>beta-test-advance-2026.git
                        <div class="pk-spec-row">
                            <span class="pk-spec-label">Lokasi</span>
                            <span class="pk-spec-colon">:</span>
                            <span class="pk-spec-value"><?= esc($product['location']) ?></span>
                        </div>
                        <div class="pk-spec-row">
                            <span class="pk-spec-label">Tag</span>
                            <span class="pk-spec-colon">:</span>
                            <span class="pk-spec-value"><?= esc(implode(', ', $product['tags'])) ?></span>
                        </div>
                    </div>

                    <div class="pk-section-heading">Deskripsi Produk</div>
                    <p class="pk-detail-desc"><?= nl2br(esc($product['description'])) ?></p>

                    <div class="pk-detail-actions">
                        <button type="button" class="pk-btn-report" onclick="openReportModal()">Laporkan</button>
                        <button type="button" class="pk-btn-buy" onclick="openBuyModal()">Ajukan Pembelian</button>
                    </div>
                </div>

            </div>

            <!-- ================= 3. RECOMMENDATIONS ================= -->
            <section class="pk-rec-section">
                <h2 class="pk-rec-title">Kamu mungkin juga suka</h2>

                <div class="pk-rec-grid">
                    <?php foreach ($recommendations as $rec): ?>
                        <div class="pk-card" onclick="window.location.href='<?= route('/barang') ?>?id=<?= $rec['id'] ?>'">
                            <div class="pk-card-top-badges">
                                <span class="pk-badge-condition"><?= esc($rec['condition']) ?></span>
                                <span class="pk-badge-category"><?= esc($rec['category']) ?></span>
                            </div>
                            <div class="pk-card-img-wrap">
                                <img src="<?= esc($rec['image']) ?>" alt="<?= esc($rec['title']) ?>" loading="lazy">
                            </div>
                            <span class="pk-card-location">📍 <?= esc($rec['location']) ?></span>
                            <h3 class="pk-card-title"><?= esc($rec['title']) ?></h3>
                            <p class="pk-card-desc"><?= esc($rec['short_desc']) ?></p>
                            <div class="pk-card-tags">
                                <?php foreach ($rec['tags'] as $tag): ?>
                                    <span class="pk-tag-pill"><?= esc($tag) ?></span>
                                <?php endforeach; ?>
                            </div>
                            <div class="pk-card-orig-price"><?= format_rupiah($rec['original_price']) ?></div>
                            <div class="pk-card-price"><?= format_rupiah($rec['price']) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

        </div>
    </main>

    <!-- ================= MODAL AJUKAN PEMBELIAN ================= -->
    <div class="pk-modal-backdrop" id="buyModal">
        <div class="pk-modal-box">
            <div style="font-size: 2.2rem; margin-bottom: 10px;">🤝</div>
            <h3 style="font-size: 1.15rem; font-weight: 800; color: var(--pk-navy); margin-bottom: 8px;">Ajukan Pembelian COD</h3>
            <p style="font-size: 0.85rem; color: #64748B; line-height: 1.5; margin-bottom: 20px;">
                Anda mengajukan pembelian untuk <strong><?= esc($product['title']) ?></strong> seharga <strong><?= format_rupiah($product['price']) ?></strong> dengan metode COD di area kampus.
            </p>
            <div style="display: flex; gap: 10px; justify-content: center;">
                <button type="button" class="pk-btn-report" onclick="closeBuyModal()">Batal</button>
                <button type="button" class="pk-btn-buy" onclick="confirmPurchase()">Konfirmasi &amp; Hubungi Penjual</button>
            </div>
        </div>
    </div>

    <!-- ================= MODAL LAPORKAN ================= -->
    <div class="pk-modal-backdrop" id="reportModal">
        <div class="pk-modal-box">
            <div style="font-size: 2.2rem; margin-bottom: 10px;">⚠️</div>
            <h3 style="font-size: 1.15rem; font-weight: 800; color: #DC2626; margin-bottom: 8px;">Laporkan Barang</h3>
            <p style="font-size: 0.85rem; color: #64748B; line-height: 1.5; margin-bottom: 20px;">
                Apakah barang ini melanggar ketentuan atau berisi informasi yang tidak sesuai?
            </p>
            <div style="display: flex; gap: 10px; justify-content: center;">
                <button type="button" class="pk-btn-buy" style="background: #94A3B8;" onclick="closeReportModal()">Batal</button>
                <button type="button" class="pk-btn-report" onclick="confirmReport()">Kirim Laporan</button>
            </div>
        </div>
    </div>

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

    <!-- JAVASCRIPT FOR GALLERY CAROUSEL & ACTIONS -->
    <script>
        const galleryImages = <?= json_encode($product['gallery']) ?>;beta-test-advance-2026.git
        let activeIdx = 0;

        function switchThumb(imgUrl, el) {
            document.getElementById('mainProductImage').src = imgUrl;
            document.querySelectorAll('.pk-thumb-item').forEach((item, idx) => {
                item.classList.remove('active');
                if (item === el) activeIdx = idx;
            });
            el.classList.add('active');
        }

        function prevThumb() {
            activeIdx = (activeIdx - 1 + galleryImages.length) % galleryImages.length;
            updateActiveThumb();
        }

        function nextThumb() {beta-test-advance-2026.git
            activeIdx = (activeIdx + 1) % galleryImages.length;
            updateActiveThumb();
        }

        function updateActiveThumb() {
            const thumbItems = document.querySelectorAll('.pk-thumb-item');
            if (thumbItems[activeIdx]) {
                switchThumb(galleryImages[activeIdx], thumbItems[activeIdx]);
            }
        }

        function openBuyModal() {
            document.getElementById('buyModal').classList.add('show');
        }

        function closeBuyModal() {
            document.getElementById('buyModal').classList.remove('show');
        }

        function confirmPurchase() {
            closeBuyModal();
            alert('Pengajuan pembelian COD berhasil dikirim ke penjual! Silakan cek menu Riwayat Pengajuan.');
        }

        function openReportModal() {
            document.getElementById('reportModal').classList.add('show');
        }

        function closeReportModal() {
            document.getElementById('reportModal').classList.remove('show');
        }

        function confirmReport() {
            closeReportModal();
            alert('Terima kasih. Laporan Anda telah kami terima untuk ditinjau oleh tim PasarKampus.');
        }
    </script>
</body>
</html>

<?php
$session = get_session();
$userName = $session['name'] ?? 'Pengguna';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PasarKampus - Riwayat Pengajuan Pembelian</title>
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
            --pk-amber-hover: #D97706;
            --pk-green: #22C55E;
            --pk-red: #EF4444;
            --pk-border: #E2E8F0;
            --pk-bg: #F8FAFC;
        }

        body.pk-riwayat-body {
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
            background: linear-gradient(180deg, #1D4ED8 0%, #2355B8 60%, #1E40AF 100%);
            height: 64px;
            display: flex;
            align-items: center;
            padding: 0 28px;
            color: #FFFFFF;
            position: sticky;
            top: 0;
            z-index: 900;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }

        .pk-nav-inner {
            width: 100%;
            max-width: 1200px;
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
            max-width: 420px;
            position: relative;
        }

        .pk-nav-search input {
            width: 100%;
            height: 38px;
            border-radius: 9999px;
            border: none;
            padding: 0 16px 0 38px;
            font-size: 0.85rem;
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

        .pk-nav-menu {
            display: flex;
            align-items: center;
            gap: 20px;
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
            text-decoration: underline;
        }

        /* 2. MAIN CONTAINER */
        .pk-main-container {
            flex: 1;
            width: 100%;
            max-width: 1060px;
            margin: 28px auto 48px;
            padding: 0 20px;
            box-sizing: border-box;
        }

        /* Header row: Title + Kembali */
        .pk-page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 22px;
        }

        .pk-page-title {
            font-size: 1.55rem;
            font-weight: 800;
            color: var(--pk-navy);
            margin: 0;
            letter-spacing: -0.4px;
        }

        .pk-back-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--pk-navy);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: color 0.15s, transform 0.15s;
        }

        .pk-back-btn:hover {
            color: #2563EB;
            transform: translateX(-3px);
        }

        /* White Card Wrapper */
        .pk-card-wrapper {
            background: #FFFFFF;
            border: 1px solid #EDF2F7;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            padding: 32px 36px 36px;
            box-sizing: border-box;
        }

        /* Table Styles (Matching Screenshot 085213) */
        .pk-table-outer {
            width: 100%;
            overflow-x: auto;
            border-radius: 12px;
            border: 1px solid #CBD5E1;
            background: #FFFFFF;
        }

        .pk-history-table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
            font-size: 0.92rem;
        }

        .pk-history-table thead tr {
            background-color: var(--pk-navy);
            color: #FFFFFF;
        }

        .pk-history-table th {
            padding: 14px 16px;
            font-weight: 700;
            font-size: 0.92rem;
            border-right: 1px solid rgba(255, 255, 255, 0.15);
            white-space: nowrap;
        }

        .pk-history-table th:last-child {
            border-right: none;
        }

        .pk-history-table tbody tr {
            border-bottom: 1px solid #E2E8F0;
            transition: background 0.15s;
        }

        .pk-history-table tbody tr:last-child {
            border-bottom: none;
        }

        .pk-history-table tbody tr:hover {
            background-color: #F8FAFC;
        }

        .pk-history-table td {
            padding: 16px 14px;
            color: #1E293B;
            border-right: 1px solid #E2E8F0;
            vertical-align: middle;
        }

        .pk-history-table td:last-child {
            border-right: none;
        }

        .pk-btn-detail {
            background-color: var(--pk-amber);
            color: #1E293B;
            border: none;
            border-radius: 6px;
            padding: 7px 18px;
            font-size: 0.82rem;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            transition: background 0.15s, transform 0.15s;
            white-space: nowrap;
        }

        .pk-btn-detail:hover {
            background-color: var(--pk-amber-hover);
            transform: translateY(-1px);
        }

        /* Detail View Styles (Matching Screenshots 085422, 085525, 085609, 085652) */
        .pk-detail-info-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
            font-size: 1.05rem;
            color: #0F172A;
        }

        .pk-detail-row {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .pk-detail-label {
            font-weight: 800;
            color: #0F172A;
            min-width: 140px;
        }

        .pk-detail-val {
            font-weight: 500;
            color: #0F172A;
        }

        /* Status Badges */
        .pk-badge-selesai {
            background-color: var(--pk-green);
            color: #FFFFFF;
            font-size: 0.72rem;
            font-weight: 800;
            padding: 3px 8px;
            border-radius: 4px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .pk-badge-proses {
            background-color: var(--pk-amber);
            color: #FFFFFF;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 4px;
        }

        .pk-badge-pending {
            background-color: var(--pk-amber);
            color: #FFFFFF;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 4px;
        }

        .pk-badge-ditolak {
            background-color: var(--pk-red);
            color: #FFFFFF;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 4px;
        }

        /* Message Box in Detail View */
        .pk-detail-message-box {
            margin-top: 32px;
            color: var(--pk-navy);
            font-size: 1.05rem;
            font-weight: 800;
            line-height: 1.5;
        }

        /* Button Hubungi WhatsApp */
        .pk-btn-wa {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: #00D757;
            color: #FFFFFF;
            font-size: 0.95rem;
            font-weight: 700;
            padding: 12px 28px;
            border-radius: 9999px;
            text-decoration: none;
            margin-top: 24px;
            box-shadow: 0 4px 14px rgba(0, 215, 87, 0.35);
            transition: background 0.2s, transform 0.15s;
        }

        .pk-btn-wa:hover {
            background-color: #00BA4B;
            transform: translateY(-1px);
        }

        /* Link Batalkan Pengajuan */
        .pk-link-batal {
            display: inline-block;
            margin-top: 14px;
            color: #F87171;
            font-size: 0.92rem;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: color 0.15s;
        }

        .pk-link-batal:hover {
            color: #EF4444;
            text-decoration: underline;
        }

        /* Button Cari Barang Lain di Katalog */
        .pk-btn-katalog {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: var(--pk-navy);
            color: #FFFFFF;
            font-size: 0.85rem;
            font-weight: 700;
            padding: 10px 24px;
            border-radius: 9999px;
            text-decoration: none;
            transition: background 0.2s, transform 0.15s;
            box-shadow: 0 4px 12px rgba(29, 53, 109, 0.2);
        }

        .pk-btn-katalog:hover {
            background-color: #162850;
            transform: translateY(-1px);
        }

        /* Section Visibility Toggle */
        .pk-view-section {
            display: none;
        }

        .pk-view-section.active {
            display: block;
        }

        @media (max-width: 768px) {
            .pk-card-wrapper {
                padding: 20px 16px;
            }
            .pk-page-title {
                font-size: 1.25rem;
            }
            .pk-nav-search {
                max-width: 200px;
            }
        }

        /* ================= MODAL POP-UP STYLES ================= */
        .pk-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(15, 23, 42, 0.45); /* Background Dimmed */
            backdrop-filter: blur(4px); /* Blur halus latar belakang */
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.25s ease, visibility 0.25s ease;
            padding: 20px;
            box-sizing: border-box;
        }

        .pk-modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .pk-modal-container {
            background: #FFFFFF;
            border-radius: 20px;
            width: 100%;
            max-width: 580px;
            padding: 36px 40px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
            position: relative;
            transform: translateY(20px) scale(0.95);
            transition: transform 0.25s ease;
            box-sizing: border-box;
        }

.pk-modal-overlay.active .pk-modal-container {
    transform: translateY(0) scale(1);
}

/* Tombol Silang (Close) */
.pk-modal-close {
    position: absolute;
    top: 28px;
    right: 28px;
    background: none;
    border: none;
    color: #EF4444;
    font-size: 1.5rem;
    font-weight: 700;
    cursor: pointer;
    line-height: 1;
    padding: 4px;
    transition: transform 0.15s, opacity 0.15s;
}

.pk-modal-close:hover {
    transform: scale(1.15);
    opacity: 0.8;
}

.pk-modal-header {
    margin-bottom: 28px;
}

.pk-modal-title {
    font-size: 1.55rem;
    font-weight: 800;
    color: var(--pk-navy);
    margin: 0;
    letter-spacing: -0.4px;
}

/* Kustom tombol aksi di dalam pop-up */
.pk-btn-wa-modal {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    background-color: #5CE67A;
    color: #FFFFFF;
    font-size: 0.92rem;
    font-weight: 700;
    padding: 10px 22px;
    border-radius: 9999px;
    text-decoration: none;
    box-shadow: 0 4px 12px rgba(92, 230, 122, 0.3);
    transition: background-color 0.2s, transform 0.15s;
}

.pk-btn-wa-modal:hover {
    background-color: #4BD468;
    transform: translateY(-1px);
}

.pk-btn-katalog-modal {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background-color: var(--pk-navy);
    color: #FFFFFF;
    font-size: 0.85rem;
    font-weight: 700;
    padding: 9px 20px;
    border-radius: 9999px;
    text-decoration: none;
    box-shadow: 0 4px 12px rgba(29, 53, 109, 0.2);
    transition: background-color 0.2s, transform 0.15s;
}

.pk-btn-katalog-modal:hover {
    background-color: #162850;
    transform: translateY(-1px);
}

/* ================= FOOTER STYLES ================= */
        .lp-footer {
            background-color: var(--pk-navy); /* Background Biru Pekat (#1D356D) */
            color: #FFFFFF;
            padding: 48px 0 28px;
            margin-top: 48px;
            font-family: inherit;
        }

        .lp-container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
            box-sizing: border-box;
        }

        .lp-footer-grid {
            display: grid;
            grid-template-columns: 2.2fr 1fr 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }

        .lp-footer-col p {
            color: #94A3B8;
            font-size: 0.9rem;
            line-height: 1.6;
            margin: 12px 0 0 0;
            max-width: 320px;
        }

        .lp-logo {
            font-size: 1.4rem;
            font-weight: 800;
            color: #FFFFFF;
            text-decoration: none;
            display: inline-block;
            letter-spacing: -0.5px;
        }

        .lp-footer-col h4 {
            color: #F59E0B; /* Warna Emas/Kuning Header Kolom */
            font-size: 0.85rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin: 0 0 18px 0;
        }

        .lp-footer-links {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .lp-footer-links a {
            color: #E2E8F0;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: color 0.2s, opacity 0.2s;
        }

        .lp-footer-links a:hover {
            color: #FFFFFF;
            text-decoration: underline;
        }

        .lp-footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.15); /* Garis Pembatas Tipis */
            padding-top: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #94A3B8;
            font-size: 0.85rem;
        }

        @media (max-width: 768px) {
            .lp-footer-grid {
                grid-template-columns: 1fr;
                gap: 28px;
            }
            .lp-footer-bottom {
                flex-direction: column;
                gap: 12px;
                text-align: center;
            }
        }
    </style>
</head>
<body class="pk-riwayat-body">

    <!-- ================= 1. TOP NAVBAR ================= -->
    <header class="pk-navbar">
        <div class="pk-nav-inner">
            <!-- Brand -->
            <a href="<?= route('/') ?>" class="pk-nav-logo">
                PasarKampus
            </a>

            <!-- Search Bar -->
            <div class="pk-nav-search">
                <span class="pk-nav-search-icon">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </span>
                <input type="text" id="riwayatSearch" placeholder="Cari barang...." onkeydown="if(event.key==='Enter') window.location.href='<?= route('/dashboard.php') ?>?q='+encodeURIComponent(this.value)">
            </div>

            <!-- Right Links -->
            <div class="pk-nav-menu">
                <a href="<?= route('/profil.php') ?>" class="pk-nav-link">Profil</a>
                <a href="<?= route('/dashboard.php') ?>" class="pk-nav-link">Katalog</a>
                <a href="<?= route('/#faq') ?>" class="pk-nav-link">butuh bantuan?</a>
            </div>
        </div>
    </header>

    <!-- ================= 2. MAIN CONTAINER ================= -->
    <main class="pk-main-container">

        <!-- ================= VIEW 1: TABLE RIWAYAT UTAMA (Screenshot 085213) ================= -->
        <section id="viewTableRiwayat" class="pk-view-section active">
            <!-- Header Row -->
            <div class="pk-page-header">
                <h1 class="pk-page-title">Riwayat Pengajuan Pembelian Saya</h1>
                <a href="javascript:void(0)" onclick="goBackFromTable()" class="pk-back-btn">
                    &larr; Kembali
                </a>
            </div>

            <!-- Card with Table -->
            <div class="pk-card-wrapper">
                <div class="pk-table-outer">
                    <table class="pk-history-table">
                        <thead>
                            <tr>
                                <th style="width: 80px;">ID</th>
                                <th>Nama Barang</th>
                                <th>Nama Penjual</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th style="width: 140px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="riwayatTableBody">
                            <!-- Populated dynamically via JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- ================= MODAL POP-UP DETAIL PENGAJUAN ================= -->
    <div id="detailModal" class="pk-modal-overlay" onclick="handleOverlayClick(event)">
        <div class="pk-modal-container">
            <!-- Tombol Silang Close -->
            <button type="button" class="pk-modal-close" onclick="closeDetailModal()" aria-label="Tutup">&times;</button>
            
            <!-- Header Pop-up -->
            <div class="pk-modal-header">
                <h2 id="modalHeaderTitle" class="pk-modal-title">Detail Pengajuan #--</h2>
            </div>
            
            <!-- Body Pop-up (Dinamis via JS) -->
            <div id="modalBodyContent">
                <!-- Data injected dynamically based on status -->
            </div>
        </div>
    </div>

    </main>
    <!-- ================= 3. FOOTER ================= -->
    <footer class="lp-footer" style="margin-top: 48px;">
        <div class="lp-container">
            <div class="lp-footer-grid">
                <!-- Col 1: Brand & Copyright -->
                <div class="lp-footer-col">
                    <a href="<?= route('/') ?>" class="lp-logo" style="color: #FFFFFF;">
                        PasarKampus
                    </a>
                    <p>
                        Platform marketplace eksklusif mahasiswa untuk sirkulasi barang perkuliahan yang aman, hemat, dan berkelanjutan.
                    </p>
                </div>

                <!-- Col 2: PasarKampus -->
                <div class="lp-footer-col">
                    <h4>PasarKampus</h4>
                    <ul class="lp-footer-links">
                        <li><a href="<?= route('/#tentang-kami') ?>">Tentang kami</a></li>
                        <li><a href="<?= route('/dashboard.php') ?>">Katalog Barang</a></li>
                        <li><a href="<?= route('/register.php') ?>">Daftar Akun Mahasiswa</a></li>
                        <li><a href="<?= route('/dashboard-penjual.php') ?>">Dashboard Penjual</a></li>
                    </ul>
                </div>

                <!-- Col 3: Bantuan -->
                <div class="lp-footer-col">
                    <h4>Bantuan</h4>
                    <ul class="lp-footer-links">
                        <li><a href="<?= route('/#faq') ?>">Pusat Bantuan</a></li>
                        <li><a href="<?= route('/#faq') ?>">FAQ</a></li>
                        <li><a href="mailto:support@pasarkampus.id">Kontak Kami</a></li>
                    </ul>
                </div>

                <!-- Col 4: Dukungan -->
                <div class="lp-footer-col">
                    <h4>Dukungan</h4>
                    <ul class="lp-footer-links">
                        <li><a href="#" onclick="alert('Kebijakan Privasi: Data kampus dan identitas mahasiswa dilindungi.'); return false;">Kebijakan</a></li>
                        <li><a href="#" onclick="alert('Panduan COD: Selalu lakukan serah terima di area kampus yang ramai pada jam operasional kampus.'); return false;">Panduan Keamanan COD</a></li>
                        <li><a href="#" onclick="alert('Syarat & Ketentuan PasarKampus: Hanya untuk barang legal & tidak melanggar hukum.'); return false;">Syarat &amp; Ketentuan</a></li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Copyright Bar -->
            <div class="lp-footer-bottom">
                <div>
                    &copy; 2026 PasarKampus. Hak Cipta Dilindungi.
                </div>
                <div>
                    Dibuat untuk Project Based Learning (PBL) Mahasiswa.
                </div>
            </div>
        </div>
    </footer>

    <!-- JavaScript logic -->
   <script>
    // Data Seed Default
    const defaultRiwayatList = [
        {
            id: 12,
            namaBarang: "Buku kalkulus",
            penjual: "Budi",
            penjualDisplay: "Arif",
            tanggal: "12 Oktober 2026",
            tanggalSelesai: "13 Oktober 2026",
            harga: 50000,
            status: "Selesai",
            teleponPenjual: "081234567890"
        },
        {
            id: 13,
            namaBarang: "Jas lab",
            penjual: "Naya",
            penjualDisplay: "Naya",
            tanggal: "16 Oktober 2026",
            tanggalDisetujui: "16 Oktober 2026",
            harga: 80000,
            status: "Proses",
            teleponPenjual: "081234567891"
        },
        {
            id: 14,
            namaBarang: "Sepatu",
            penjual: "James",
            penjualDisplay: "James",
            tanggal: "22 Oktober 2026",
            harga: 100000,
            status: "Pending",
            teleponPenjual: "081234567892"
        },
        {
            id: 15,
            namaBarang: "Kipas angin",
            penjual: "Citra",
            penjualDisplay: "Citra",
            tanggal: "12 November 2026",
            harga: 65000,
            status: "Ditolak",
            teleponPenjual: "081234567893"
        }
    ];

    function getRiwayatData() {
        const stored = localStorage.getItem('pk_user_riwayat');
        if (stored) {
            try { return JSON.parse(stored); } catch(e) {}
        }
        localStorage.setItem('pk_user_riwayat', JSON.stringify(defaultRiwayatList));
        return defaultRiwayatList;
    }

    function saveRiwayatData(data) {
        localStorage.setItem('pk_user_riwayat', JSON.stringify(data));
    }

    function formatRupiah(num) {
        return new Intl.NumberFormat('id-ID').format(num);
    }

    function renderTable() {
        const list = getRiwayatData();
        const tbody = document.getElementById('riwayatTableBody');
        if (!tbody) return;

        if (list.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" style="padding: 24px; color: #94A3B8;">Belum ada riwayat pengajuan pembelian.</td></tr>`;
            return;
        }

        tbody.innerHTML = list.map(item => `
            <tr>
                <td style="font-weight: 700;">${item.id}</td>
                <td>${item.namaBarang}</td>
                <td>${item.penjualDisplay || item.penjual}</td>
                <td>${item.tanggal}</td>
                <td>${item.status}</td>
                <td>
                    <button type="button" class="pk-btn-detail" onclick="showDetail(${item.id})">Lihat Detail</button>
                </td>
            </tr>
        `).join('');
    }

    // FUNGSI UTAMA: MENAMPILKAN POP-UP MODAL
    function showDetail(id) {
        const list = getRiwayatData();
        const item = list.find(r => r.id === parseInt(id));
        if (!item) {
            alert('Data pengajuan tidak ditemukan.');
            return;
        }

        // Set Judul Modal
        document.getElementById('modalHeaderTitle').innerText = `Detail Pengajuan #${item.id}`;

        const container = document.getElementById('modalBodyContent');
        let statusBadge = '';
        let extraInfo = '';
        let bottomActions = '';

        const statusLower = item.status.toLowerCase();

        // Rendering konten sesuai status (Tampilan disesuaikan dengan desain screenshot pop-up)
        if (statusLower === 'selesai') {
            statusBadge = `<span class="pk-badge-selesai">SELESAI</span>`;
            extraInfo = `
                <div class="pk-detail-row">
                    <span class="pk-detail-label">Tanggal selesai :</span>
                    <span class="pk-detail-val">${item.tanggalSelesai || '13 Oktober 2026'}</span>
                </div>
            `;
            bottomActions = `
                <div class="pk-detail-message-box" style="margin-top: 28px;">
                    <p style="margin: 0 0 6px 0;">Transaksi berhasil diselesaikan.</p>
                    <p style="margin: 0;">Terima kasih telah bertransaksi secara aman di Pasar Kampus!</p>
                </div>
            `;
        } else if (statusLower === 'proses') {
            statusBadge = `<span class="pk-badge-proses">Proses</span>`;
            extraInfo = `
                <div class="pk-detail-row">
                    <span class="pk-detail-label">Tanggal disetujui :</span>
                    <span class="pk-detail-val">${item.tanggalDisetujui || '16 Oktober 2026'}</span>
                </div>
            `;
            const waPhone = (item.teleponPenjual || '081234567890').replace(/^0/, '62');
            const waText = encodeURIComponent(`Halo ${item.penjual}, saya ingin berkoordinasi terkait pengajuan pembelian #${item.id} (${item.namaBarang}) via Pasar Kampus.`);
            bottomActions = `
                <div class="pk-detail-message-box" style="margin-top: 28px;">
                    <p style="margin: 0 0 4px 0;">Pengajuan telah disetujui penjual!</p>
                    <p style="margin: 0 0 20px 0;">Segera hubungi penjual via WhatsApp untuk menyepakati lokasi &amp; waktu COD.</p>
                </div>
                <div style="display: flex; align-items: center; gap: 20px;">
                    <a href="https://wa.me/${waPhone}?text=${waText}" target="_blank" class="pk-btn-wa-modal">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/>
                        </svg>
                        Hubungi penjual
                    </a>
                    <a href="javascript:void(0)" onclick="batalkanPengajuan(${item.id})" class="pk-link-batal" style="margin-top: 0;">
                        Batalkan pengajuan
                    </a>
                </div>
            `;
        } else if (statusLower === 'pending') {
            statusBadge = `<span class="pk-badge-pending">Pending</span>`;
            bottomActions = `
                <div class="pk-detail-message-box" style="margin-top: 28px; margin-bottom: 20px;">
                    <p style="margin: 0;">Menunggu konfirmasi penjual...</p>
                </div>
                <div>
                    <a href="javascript:void(0)" onclick="batalkanPengajuan(${item.id})" class="pk-link-batal" style="margin-top: 0;">
                        Batalkan pengajuan
                    </a>
                </div>
            `;
        } else if (statusLower === 'ditolak') {
            statusBadge = `<span class="pk-badge-ditolak">Ditolak</span>`;
            bottomActions = `
                <div class="pk-detail-message-box" style="margin-top: 28px;">
                    <p style="margin: 0 0 4px 0;">Pengajuan ini telah ditolak oleh penjual.</p>
                    <p style="margin: 0 0 24px 0;">Silakan cari barang sejenis di katalog.</p>
                </div>
                <div>
                    <a href="<?= route('/dashboard.php') ?>" class="pk-btn-katalog-modal">
                        Cari barang lain di katalog
                    </a>
                </div>
            `;
        }

        container.innerHTML = `
            <div class="pk-detail-info-list">
                <div class="pk-detail-row">
                    <span class="pk-detail-label">Barang :</span>
                    <span class="pk-detail-val">${item.namaBarang}</span>
                </div>
                <div class="pk-detail-row">
                    <span class="pk-detail-label">Penjual :</span>
                    <span class="pk-detail-val">${item.penjual}</span>
                </div>
                <div class="pk-detail-row">
                    <span class="pk-detail-label">Harga :</span>
                    <span class="pk-detail-val">Rp. ${formatRupiah(item.harga)}</span>
                </div>
                <div class="pk-detail-row">
                    <span class="pk-detail-label">Status :</span>
                    <div>${statusBadge}</div>
                </div>
                ${extraInfo}
            </div>
            ${bottomActions}
        `;

        // Buka Pop-up & Kunci scroll halaman belakang
        document.getElementById('detailModal').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    // FUNGSI PENUTUP MODAL
    function closeDetailModal() {
        document.getElementById('detailModal').classList.remove('active');
        document.body.style.overflow = '';
    }

    function handleOverlayClick(event) {
        if (event.target.id === 'detailModal') {
            closeDetailModal();
        }
    }

    // Tutup modal jika pengguna menekan tombol ESC
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeDetailModal();
        }
    });

    function batalkanPengajuan(id) {
        if (confirm(`Apakah Anda yakin ingin membatalkan pengajuan #${id}?`)) {
            let list = getRiwayatData();
            const item = list.find(r => r.id === id);
            if (item) {
                item.status = "Dibatalkan";
                saveRiwayatData(list);
                closeDetailModal();
                renderTable();
                alert(`Pengajuan #${id} berhasil dibatalkan.`);
            }
        }
    }

    function goBackFromTable() {
        if (window.history.length > 1) {
            window.history.back();
        } else {
            window.location.href = '<?= route('/dashboard.php') ?>';
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        renderTable();
    });
</script>
</body>
</html>

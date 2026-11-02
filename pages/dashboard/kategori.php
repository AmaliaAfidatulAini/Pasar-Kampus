<?php
$session = get_session();
$adminName = $session['name'] ?? 'Superadmin Pusat';
$activeTab = 'kategori';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Kategori - Portal Superadmin Pasar Kampus</title>
    <!-- Google Fonts Inter & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <!-- CSS Stylesheets -->
    <link rel="stylesheet" href="<?= asset('/css/style.css') ?>">
    <link rel="stylesheet" href="<?= asset('/css/admin-style.css') ?>">
</head>
<body class="admin-body">

    <!-- =========================================================
         LAYOUT UTAMA ADMIN (Sidebar Sesuai Wireframe Layout Dashboard)
         ========================================================= -->
    <div class="admin-layout">

        <!-- ---------------- SIDEBAR ADMIN ---------------- -->
        <aside id="adminSidebar" class="admin-sidebar">
            <div class="admin-sidebar-top">
                <!-- POJOK KIRI ATAS: PROFILE -->
                <div class="admin-profile-box">
                    <div class="admin-profile-avatar-wrap">
                        <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=200" alt="Avatar Admin" class="admin-profile-avatar">
                        <span class="admin-profile-status-dot" title="Online Live Monitoring"></span>
                    </div>
                    <div class="admin-profile-info">
                        <div class="admin-profile-name" title="Superadmin Pusat">Superadmin Pusat</div>
                        <span class="admin-profile-role">Super Admin</span>
                    </div>
                </div>

                <!-- MENU ADMIN -->
                <div class="admin-nav-section">
                    <div class="admin-nav-title">MENU ADMIN</div>
                    <ul class="admin-nav-list">
                        <li>
                            <a href="<?= route('/dashboard/index.php') ?>" id="nav-dashboard" class="admin-nav-link <?= $activeTab === 'dashboard' ? 'active' : '' ?>">
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= route('/dashboard/user.php') ?>" id="nav-users" class="admin-nav-link <?= $activeTab === 'users' ? 'active' : '' ?>">
                                <span>Kelola User</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= route('/dashboard/barang.php') ?>" id="nav-barang" class="admin-nav-link <?= $activeTab === 'barang' ? 'active' : '' ?>">
                                <span>Barang</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= route('/dashboard/pengajuan.php') ?>" id="nav-pengajuan" class="admin-nav-link <?= $activeTab === 'pengajuan' ? 'active' : '' ?>">
                                <span>Pengajuan</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= route('/dashboard/report.php') ?>" id="nav-report" class="admin-nav-link <?= $activeTab === 'report' ? 'active' : '' ?>">
                                <span>Report</span>
                                <span id="sidebarReportBadge" class="admin-nav-badge">2</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= route('/dashboard/kategori.php') ?>" id="nav-kategori" class="admin-nav-link <?= $activeTab === 'kategori' ? 'active' : '' ?>">
                                <span>Kategori</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- SIDEBAR FOOTER -->
            <div class="admin-sidebar-footer">
                <button type="button" class="admin-btn-logout" onclick="handleAdminLogout()">
                    <span>[Logout]</span>
                </button>
                <a href="<?= route('/dashboard.php') ?>" target="_blank" class="admin-btn-back-katalog">
                    <span>Lihat Web Pengguna &rarr;</span>
                </a>
            </div>
        </aside>

        <!-- ---------------- MAIN CONTENT AREA ---------------- -->
        <main class="admin-main">
            <!-- Top Header Bar -->
            <header class="admin-topbar">
                <div class="admin-topbar-left">
                    <button type="button" class="admin-mobile-toggle" onclick="toggleMobileSidebar()" title="Toggle Sidebar">
                        ☰
                    </button>
                    <div class="admin-breadcrumb">
                        <span>Portal Admin / </span>
                        <strong id="adminCurrentBreadcrumb">Master Kategori</strong>
                    </div>
                </div>

                <div class="admin-topbar-right">
                    <div class="admin-live-indicator" title="Sistem memantau seluruh aktivitas website kampus">
                        <span class="admin-pulse-dot"></span>
                        <span>Live Monitoring Website</span>
                    </div>
                </div>
            </header>

            <div class="admin-content-container">

                <!-- NOTE SISTEM BANNER SESUAI SPESIFIKASI -->
                <div class="admin-monitoring-banner">
                    <div class="admin-monitoring-banner-left">
                        <div>
                            <h4>Sistem Pengawasan Pusat Pasar Kampus</h4>
                            <p>Halaman ini memantau seluruh ekosistem website (UB, UM, UMM): katalog barang, riwayat pengajuan COD, serta integritas komunitas mahasiswa secara realtime.</p>
                        </div>
                    </div>
                </div>

                <!-- =========================================================
                     VIEW 6: KATEGORI (Master Kategori / Tag)
                     (Sesuai Wireframe 'Screenshot 2026-10-29 120326.png')
                     ========================================================= -->
                <section id="view-kategori" class="admin-view-panel active">
                    <div class="admin-page-header">
                        <div class="admin-header-title-wrap">
                            <span class="admin-endpoint-badge">[ GET /kategori ] - Halaman Daftar Kategori / Master Tag</span>
                            <h1>Manajemen Kategori / Master Tag</h1>
                            <p>Deskripsi: Kelola kategori bawaan untuk filter katalog.</p>
                        </div>
                        <!-- Tombol Tambah Kategori Sesuai Wireframe [+ Tambah] -->
                        <button type="button" class="btn-admin-primary" onclick="openAddKategoriModal()">
                            <span>+ Tambah Kategori</span>
                        </button>
                    </div>

                    <!-- Table Sesuai Wireframe: ID | Nama Kategori | Jumlah Barang | Status | Aksi ([Edit][Del]) -->
                    <div class="admin-table-card">
                        <div class="admin-table-responsive">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th style="width: 70px;">ID</th>
                                        <th>Nama Kategori & Deskripsi</th>
                                        <th>Jumlah Barang</th>
                                        <th>Status</th>
                                        <th style="width: 170px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="kategoriTableBody">
                                    <!-- Populated dynamically via JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

            </div>
        </main>
    </div>

    <!-- ====================================================================
         MODALS (Kategori Management)
         ==================================================================== -->

    <!-- MODAL ADMIN FORM TAMBAH / EDIT KATEGORI -->
    <div id="modalKategori" class="admin-modal-backdrop">
        <div class="admin-modal-card">
            <div class="admin-modal-header">
                <div>
                    <button type="button" class="admin-modal-back-btn" onclick="closeModal('modalKategori')">&lt; Kembali ke Daftar Kategori</button>
                    <h2 id="kategoriModalTitle" class="admin-modal-title">TAMBAH KATEGORI BARU</h2>
                </div>
                <button type="button" class="admin-modal-close" onclick="closeModal('modalKategori')">&times;</button>
            </div>
            <form id="kategoriForm" onsubmit="handleKategoriSubmit(event)">
                <input type="hidden" id="kategoriId">
                <div class="admin-modal-body">
                    <div class="admin-form-group">
                        <label class="admin-form-label">Nama Kategori <span class="req">*</span></label>
                        <input type="text" id="kategoriNama" required class="admin-form-input" placeholder="Contoh: Perlengkapan Kos, Buku, Elektronik">
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">Keterangan / Deskripsi</label>
                        <input type="text" id="kategoriDeskripsi" class="admin-form-input" placeholder="Deskripsi peruntukan kategori filter">
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">Status</label>
                        <div class="admin-radio-group" style="flex-direction: row; gap: 20px;">
                            <label class="admin-radio-label" style="flex: 1;">
                                <input type="radio" name="kategoriStatus" value="Aktif" checked>
                                <span>(o) <strong>Aktif</strong></span>
                            </label>
                            <label class="admin-radio-label" style="flex: 1;">
                                <input type="radio" name="kategoriStatus" value="Nonaktif">
                                <span>( ) <strong>Nonaktif</strong></span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="admin-modal-footer">
                    <button type="button" class="btn-admin-secondary" onclick="closeModal('modalKategori')">[ Batal ]</button>
                    <button type="submit" class="btn-admin-primary">[ Simpan Kategori ]</button>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript Files -->
    <script>window.CURRENT_ADMIN_PAGE = 'kategori';</script>
    <script src="<?= asset('/js/admin.js') ?>"></script>
</body>
</html>

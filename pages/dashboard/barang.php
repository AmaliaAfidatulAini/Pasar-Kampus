<?php
$session = get_session();
$adminName = $session['name'] ?? 'Superadmin Pusat';
$activeTab = 'barang';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Barang - Portal Superadmin Pasar Kampus</title>
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
                        <strong id="adminCurrentBreadcrumb">Manajemen Barang Katalog</strong>
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
                     VIEW 3: CRUD ADMIN BARANG (Barang Katalog)
                     (Sesuai Wireframe 'Screenshot 2026-10-29 120102.png')
                     ========================================================= -->
                <section id="view-barang" class="admin-view-panel active">
                    <div class="admin-page-header">
                        <div class="admin-header-title-wrap">
                            <h1>Manajemen Barang Katalog</h1>
                            <p>Monitoring barang preloved mahasiswa, verifikasi kelayakan foto, dan take-down barang palsu/melanggar</p>
                        </div>
                    </div>

                    <div class="admin-table-card">
                        <div class="admin-table-toolbar">
                            <div class="admin-search-wrap">
                                <input type="text" id="barangSearchInput" class="admin-search-input" placeholder="Cari nama barang atau penjual..." oninput="renderBarangTable()">
                            </div>
                            <div class="admin-toolbar-actions">
                                <select id="barangFilterKategori" class="admin-select-filter" onchange="renderBarangTable()">
                                    <option value="all">Semua Kategori</option>
                                    <option value="Pakaian">Pakaian</option>
                                    <option value="Buku">Buku</option>
                                    <option value="Elektronik">Elektronik</option>
                                    <option value="Perlengkapan Kos">Perlengkapan Kos</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                                <select id="barangFilterStatus" class="admin-select-filter" onchange="renderBarangTable()">
                                    <option value="all">Semua Status</option>
                                    <option value="Tersedia">Tersedia</option>
                                    <option value="Process">Process</option>
                                    <option value="Dihapus">Dihapus (Take Down)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Table Sesuai Wireframe: ID | Nama Barang | Penjual | Kategori | Status | Aksi ([Detail][Edit]) -->
                        <div class="admin-table-responsive">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th style="width: 70px;">ID</th>
                                        <th>Nama Barang</th>
                                        <th>Penjual</th>
                                        <th>Kategori</th>
                                        <th>Status</th>
                                        <th style="width: 180px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="barangTableBody">
                                    <!-- Populated dynamically via JS -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination Footer Sesuai Wireframe: [<] [1] [>] -->
                        <div class="admin-table-footer">
                            <span>Menampilkan seluruh katalog barang termonitor</span>
                            <div class="admin-pagination">
                                <button type="button" class="admin-page-btn">&lt;</button>
                                <button type="button" class="admin-page-btn active">1</button>
                                <button type="button" class="admin-page-btn">2</button>
                                <button type="button" class="admin-page-btn">&gt;</button>
                            </div>
                        </div>
                    </div>
                </section>

            </div>
        </main>
    </div>

    <!-- ====================================================================
         MODALS (Barang Management)
         ==================================================================== -->

    <!-- 1. MODAL ADMIN FORM EDIT STATUS BARANG -->
    <div id="modalEditBarang" class="admin-modal-backdrop">
        <div class="admin-modal-card">
            <div class="admin-modal-header">
                <div>
                    <button type="button" class="admin-modal-back-btn" onclick="closeModal('modalEditBarang')">&lt; Kembali ke Daftar Barang</button>
                    <h2 class="admin-modal-title">EDIT STATUS BARANG</h2>
                </div>
                <button type="button" class="admin-modal-close" onclick="closeModal('modalEditBarang')">&times;</button>
            </div>
            <form id="editBarangForm" onsubmit="handleEditBarangSubmit(event)">
                <input type="hidden" id="editBarangId">
                <div class="admin-modal-body">
                    <!-- Informasi Barang (Read Only) -->
                    <div class="admin-info-box">
                        <div class="admin-info-box-title">Informasi Barang</div>
                        <div class="admin-info-row">
                            <span class="admin-info-label">Nama Barang :</span>
                            <span id="editBarangNamaView" class="admin-info-value">Helm Bogo (ID: 11) - [Read Only]</span>
                        </div>
                        <div class="admin-info-row">
                            <span class="admin-info-label">Penjual :</span>
                            <span id="editBarangPenjualView" class="admin-info-value">Budi (@budi_ig) - [Read Only]</span>
                        </div>
                    </div>

                    <!-- Tindakan Admin -->
                    <div class="admin-form-group">
                        <label class="admin-form-label">Tindakan Admin</label>
                        <div class="admin-radio-group">
                            <label class="admin-radio-label">
                                <input type="radio" name="editBarangTindakan" value="Tersedia">
                                <span>( ) <strong>Biarkan Tersedia</strong> - Barang tetap tayang di katalog publik</span>
                            </label>
                            <label class="admin-radio-label">
                                <input type="radio" name="editBarangTindakan" value="Dihapus">
                                <span>(o) <strong>Dihapus (Take Down)</strong> - Tarik barang dari peredaran katalog</span>
                            </label>
                        </div>
                    </div>

                    <!-- Alasan Hapus -->
                    <div class="admin-form-group">
                        <label class="admin-form-label">Alasan Hapus</label>
                        <textarea id="editBarangAlasan" class="admin-form-textarea" placeholder="Contoh: Barang terindikasi tidak sesuai dengan foto / palsu"></textarea>
                    </div>
                </div>
                <div class="admin-modal-footer">
                    <button type="button" class="btn-admin-secondary" onclick="closeModal('modalEditBarang')">[ Batal ]</button>
                    <button type="submit" class="btn-admin-primary">[ Simpan Perubahan ]</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 2. MODAL ADMIN DETAIL STATUS BARANG -->
    <div id="modalDetailBarang" class="admin-modal-backdrop">
        <div class="admin-modal-card" style="max-width: 680px;">
            <div class="admin-modal-header">
                <div>
                    <button type="button" class="admin-modal-back-btn" onclick="closeModal('modalDetailBarang')">&lt; Kembali ke Daftar Barang</button>
                    <h2 class="admin-modal-title">DETAIL BARANG &amp; RIWAYAT PENGAJUAN</h2>
                </div>
                <button type="button" class="admin-modal-close" onclick="closeModal('modalDetailBarang')">&times;</button>
            </div>
            <div class="admin-modal-body">
                <!-- Informasi Barang (Read-Only) -->
                <div class="admin-info-box">
                    <div class="admin-info-box-title">Informasi Barang (Read-Only)</div>
                    <div class="admin-info-row">
                        <span class="admin-info-label">Nama Barang :</span>
                        <span id="detailBarangNama" class="admin-info-value">Helm Bogo (ID: 11)</span>
                    </div>
                    <div class="admin-info-row">
                        <span class="admin-info-label">Penjual :</span>
                        <span id="detailBarangPenjual" class="admin-info-value">Budi (@budi_ig)</span>
                    </div>
                    <div class="admin-info-row">
                        <span class="admin-info-label">Status Barang :</span>
                        <span id="detailBarangStatus" class="admin-info-value" style="color: #D97706;">PROCESS (Sedang dalam proses COD)</span>
                    </div>
                </div>

                <!-- Daftar Pengajuan Masuk (Read-Only) -->
                <div style="margin-top: 18px;">
                    <div style="font-size: 13px; font-weight: 700; color: #1E293B; margin-bottom: 6px;">Daftar Pengajuan Masuk (Read-Only)</div>
                    <table class="admin-subtable">
                        <thead>
                            <tr>
                                <th>Pembeli</th>
                                <th>Kampus</th>
                                <th>Waktu Pengajuan</th>
                                <th>Status Pengajuan</th>
                            </tr>
                        </thead>
                        <tbody id="detailBarangPengajuanBody">
                            <!-- Populated dynamically via JS -->
                        </tbody>
                    </table>
                </div>

                <!-- Catatan Sistem -->
                <div class="admin-system-notice">
                    *Catatan Sistem: Hak akses Admin dibatasi. Admin hanya dapat memantau status transaksi tanpa bisa mengubah keputusan ACC/Tolak penjual.
                </div>
            </div>
            <div class="admin-modal-footer">
                <button type="button" class="btn-admin-primary" onclick="closeModal('modalDetailBarang')">[ Tutup Detail ]</button>
            </div>
        </div>
    </div>

    <!-- JavaScript Files -->
    <script>window.CURRENT_ADMIN_PAGE = 'barang';</script>
    <script src="<?= asset('/js/admin.js') ?>"></script>
</body>
</html>

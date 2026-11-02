<?php
$session = get_session();
$adminName = $session['name'] ?? 'Superadmin Pusat';
$activeTab = 'dashboard';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Portal Superadmin Pasar Kampus</title>
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
                <!-- POJOK KIRI ATAS: PROFILE (Sesuai Wireframe Layout Dashboard Admin) -->
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

                <!-- MENU ADMIN (Sesuai Wireframe Dashboard admin) -->
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

            <!-- SIDEBAR FOOTER (Logout Sesuai Wireframe Layout) -->
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
                        <strong id="adminCurrentBreadcrumb">Dashboard Analitik</strong>
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
                     VIEW 1: DASHBOARD ANALITIK SUPERADMIN
                     ========================================================= -->
                <section id="view-dashboard" class="admin-view-panel active">
                    <div class="admin-page-header">
                        <div class="admin-header-title-wrap">
                            <h1>Dashboard Analitik Superadmin</h1>
                            <p>Ringkasan metrik statistik dan persebaran pengguna aktif di seluruh kampus mitra</p>
                        </div>
                    </div>

                    <!-- 4 METRIC STAT CARDS (Sesuai Wireframe Dashboard admin) -->
                    <div class="admin-stats-grid">
                        <!-- Card 1: Total Barang di Katalog -->
                        <div class="admin-stat-card">
                            <div class="admin-stat-header">
                                <span class="admin-stat-title">Total Barang di Katalog</span>
                            </div>
                            <div id="statTotalBarang" class="admin-stat-value">1,245</div>
                            <div class="admin-stat-meta">
                                <span class="admin-badge-stat-up">↑ +12%</span>
                                <span>dibandingkan bulan lalu</span>
                            </div>
                        </div>

                        <!-- Card 2: Jml Kategori Tersedia -->
                        <div class="admin-stat-card">
                            <div class="admin-stat-header">
                                <span class="admin-stat-title">Jml Kategori Tersedia</span>
                            </div>
                            <div id="statJmlKategori" class="admin-stat-value">15</div>
                            <div class="admin-stat-meta">
                                <span style="color: #15803D; font-weight: 600;">Semua Aktif</span>
                                <span>Master tags katalog</span>
                            </div>
                        </div>

                        <!-- Card 3: Jml Report Masuk -->
                        <div class="admin-stat-card">
                            <div class="admin-stat-header">
                                <span class="admin-stat-title">Jml Report Masuk</span>
                            </div>
                            <div id="statJmlReport" class="admin-stat-value">28</div>
                            <div class="admin-stat-meta">
                                <span style="color: #DC2626; font-weight: 700;">Butuh Tinjauan Segera</span>
                            </div>
                        </div>

                        <!-- Card 4: Jml User Aktif -->
                        <div class="admin-stat-card">
                            <div class="admin-stat-header">
                                <span class="admin-stat-title">Jml User Aktif</span>
                            </div>
                            <div id="statJmlUser" class="admin-stat-value">3,400</div>
                            <div class="admin-stat-meta">
                                <span class="admin-badge-stat-up">↑ +8.5%</span>
                                <span>Mahasiswa terverifikasi</span>
                            </div>
                        </div>
                    </div>

                    <!-- TWO COLUMNS: DIAGRAM PIE & AKTIVITAS MONITORING WEBSITE -->
                    <div class="admin-analytics-row">
                        <!-- Kolom 1: Diagram Pie Jumlah User per Kampus (Sesuai Wireframe) -->
                        <div class="admin-card">
                            <div class="admin-card-header">
                                <div>
                                    <h3 class="admin-card-title">Diagram Pie: Jumlah User per Kampus</h3>
                                    <p class="admin-card-subtitle">Persebaran mahasiswa terdaftar di kota Malang</p>
                                </div>
                            </div>

                            <div class="admin-pie-container">
                                <!-- Pie Chart SVG Visual -->
                                <div class="admin-pie-visual-wrap">
                                    <svg class="admin-pie-chart-svg" viewBox="0 0 36 36">
                                        <!-- UB: 40% (Dasharray 40 60) -->
                                        <circle cx="18" cy="18" r="15.915" fill="transparent" stroke="#1D356D" stroke-width="3.8" stroke-dasharray="40 60" stroke-dashoffset="0"></circle>
                                        <!-- UM: 35% (Dasharray 35 65, Offset -40) -->
                                        <circle cx="18" cy="18" r="15.915" fill="transparent" stroke="#2563EB" stroke-width="3.8" stroke-dasharray="35 65" stroke-dashoffset="-40"></circle>
                                        <!-- UMM: 25% (Dasharray 25 75, Offset -75) -->
                                        <circle cx="18" cy="18" r="15.915" fill="transparent" stroke="#F59E0B" stroke-width="3.8" stroke-dasharray="25 75" stroke-dashoffset="-75"></circle>
                                    </svg>
                                    <div class="admin-pie-center-label">
                                        <span>Total</span>
                                        <strong>3.4K</strong>
                                    </div>
                                </div>

                                <!-- Keterangan Sesuai Wireframe -->
                                <div class="admin-pie-legend">
                                    <div class="admin-pie-legend-item">
                                        <div class="admin-legend-dot-name">
                                            <span class="admin-legend-color-dot" style="background: #1D356D;"></span>
                                            <span>Univ. Brawijaya (UB)</span>
                                        </div>
                                        <span class="admin-legend-val">40%</span>
                                    </div>
                                    <div class="admin-pie-legend-item">
                                        <div class="admin-legend-dot-name">
                                            <span class="admin-legend-color-dot" style="background: #2563EB;"></span>
                                            <span>Univ. N. Malang (UM)</span>
                                        </div>
                                        <span class="admin-legend-val">35%</span>
                                    </div>
                                    <div class="admin-pie-legend-item">
                                        <div class="admin-legend-dot-name">
                                            <span class="admin-legend-color-dot" style="background: #F59E0B;"></span>
                                            <span>Univ. Muh. Malang (UMM)</span>
                                        </div>
                                        <span class="admin-legend-val">25%</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Kolom 2: Live Activity Monitor (Memantau Semua Websitenya) -->
                        <div class="admin-card">
                            <div class="admin-card-header">
                                <div>
                                    <h3 class="admin-card-title">Live Activity Monitor</h3>
                                    <p class="admin-card-subtitle">Aktivitas transaksi, katalog, dan interaksi pengguna terkini</p>
                                </div>
                                <span class="admin-badge admin-badge-success">Live Feed</span>
                            </div>

                            <div class="admin-activity-list">
                                <div class="admin-activity-item act-success">
                                    <div class="admin-activity-body">
                                        <div class="admin-activity-text">Pengajuan COD baru: <strong>Doni Pratama (UMM)</strong> mengajukan beli <em>Helm Bogo</em> ke <strong>Budi (UB)</strong>.</div>
                                        <div class="admin-activity-time">5 menit yang lalu • Transaksi Terjadwal</div>
                                    </div>
                                </div>
                                <div class="admin-activity-item act-danger">
                                    <div class="admin-activity-body">
                                        <div class="admin-activity-text">Laporan aduan baru <strong>#90</strong> masuk dari <strong>Citra Lestari</strong> perihal indikasi barang palsu.</div>
                                        <div class="admin-activity-time">22 menit yang lalu • Perlu Moderasi</div>
                                    </div>
                                </div>
                                <div class="admin-activity-item">
                                    <div class="admin-activity-body">
                                        <div class="admin-activity-text">Barang baru ditambahkan ke katalog: <em>"Buku Kalkulus Edisi 8"</em> oleh <strong>Farhan (UB)</strong>.</div>
                                        <div class="admin-activity-time">1 jam yang lalu • Kategori: Buku</div>
                                    </div>
                                </div>
                                <div class="admin-activity-item act-warn">
                                    <div class="admin-activity-body">
                                        <div class="admin-activity-text">Akun pengguna <strong>Budi Santoso</strong> status diubah menjadi <em>Suspend</em> karena penalti hit-and-run COD.</div>
                                        <div class="admin-activity-time">3 jam yang lalu • Tindakan Admin</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

    <!-- ====================================================================
         MODALS & FORMS (EXACTLY CORRESPONDING TO ALL WIREFRAMES)
         ==================================================================== -->

    <!-- 1. MODAL ADMIN FORM TAMBAH USER (Sesuai Wireframe Screenshot 2026-10-29 114058) -->
    <div id="modalAddUser" class="admin-modal-backdrop">
        <div class="admin-modal-card">
            <div class="admin-modal-header">
                <div>
                    <button type="button" class="admin-modal-back-btn" onclick="closeModal('modalAddUser')">&lt; Kembali ke Daftar Pengguna</button>
                    <h2 class="admin-modal-title">TAMBAH PENGGUNA BARU</h2>
                </div>
                <button type="button" class="admin-modal-close" onclick="closeModal('modalAddUser')">&times;</button>
            </div>
            <form id="addUserForm" onsubmit="handleAddUserSubmit(event)">
                <div class="admin-modal-body">
                    <div class="admin-form-group">
                        <label class="admin-form-label">Nama Lengkap <span class="req">*</span></label>
                        <input type="text" id="newUserName" required class="admin-form-input" placeholder="Masukkan nama lengkap mahasiswa">
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">NIM <span class="req">*</span></label>
                        <input type="text" id="newUserNim" required class="admin-form-input" placeholder="Contoh: 2141720012">
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">Email Kampus <span class="req">*</span></label>
                        <input type="email" id="newUserEmail" required class="admin-form-input" placeholder="Contoh: mahasiswa@student.ub.ac.id">
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">Asal Kampus</label>
                        <select id="newUserKampus" class="admin-form-select">
                            <option value="Universitas Brawijaya">Universitas Brawijaya (UB)</option>
                            <option value="Universitas Negeri Malang">Universitas Negeri Malang (UM)</option>
                            <option value="Univ. Muhammadiyah Malang">Univ. Muhammadiyah Malang (UMM)</option>
                            <option value="Politeknik Negeri Malang">Politeknik Negeri Malang (POLINEMA)</option>
                        </select>
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">Kata Sandi <span class="req">*</span></label>
                        <input type="password" id="newUserPassword" required class="admin-form-input" placeholder="Minimal 6 karakter" value="password123">
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">Role Akun</label>
                        <div class="admin-radio-group" style="flex-direction: row; gap: 20px;">
                            <label class="admin-radio-label" style="flex: 1;">
                                <input type="radio" name="newUserRole" value="Mahasiswa" checked>
                                <span>Mahasiswa (Pengguna/Penjual)</span>
                            </label>
                            <label class="admin-radio-label" style="flex: 1;">
                                <input type="radio" name="newUserRole" value="Admin">
                                <span>Admin</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="admin-modal-footer">
                    <button type="button" class="btn-admin-secondary" onclick="closeModal('modalAddUser')">[ Batal ]</button>
                    <button type="submit" class="btn-admin-primary">[ Simpan Pengguna ]</button>
                </div>
            </form>
        </div>
    </div>


    <!-- 2. MODAL ADMIN FORM EDIT STATUS USER (Sesuai Wireframe Screenshot 2026-10-29 114058) -->
    <div id="modalEditUser" class="admin-modal-backdrop">
        <div class="admin-modal-card">
            <div class="admin-modal-header">
                <div>
                    <button type="button" class="admin-modal-back-btn" onclick="closeModal('modalEditUser')">&lt; Kembali ke Daftar Pengguna</button>
                    <h2 class="admin-modal-title">EDIT STATUS PENGGUNA</h2>
                </div>
                <button type="button" class="admin-modal-close" onclick="closeModal('modalEditUser')">&times;</button>
            </div>
            <form id="editUserForm" onsubmit="handleEditUserSubmit(event)">
                <input type="hidden" id="editUserId">
                <div class="admin-modal-body">
                    <!-- Informasi Pengguna (Read Only) Sesuai Wireframe -->
                    <div class="admin-info-box">
                        <div class="admin-info-box-title">Informasi Pengguna</div>
                        <div class="admin-info-row">
                            <span class="admin-info-label">Nama Lengkap :</span>
                            <span id="editUserNameView" class="admin-info-value">Budi Santoso (NIM: 2241...) - [Read Only]</span>
                        </div>
                        <div class="admin-info-row">
                            <span class="admin-info-label">Asal Kampus :</span>
                            <span id="editUserKampusView" class="admin-info-value">Universitas Negeri Malang - [Read Only]</span>
                        </div>
                    </div>

                    <!-- Ubah Status Sesuai Wireframe: ( ) Aktif (o) Suspend ( ) Banned -->
                    <div class="admin-form-group">
                        <label class="admin-form-label">Ubah Status</label>
                        <div class="admin-radio-group">
                            <label class="admin-radio-label">
                                <input type="radio" name="editUserStatus" value="Aktif">
                                <span>( ) <strong>Aktif</strong> - Akun dapat bertransaksi, pasang barang, dan COD normal</span>
                            </label>
                            <label class="admin-radio-label">
                                <input type="radio" name="editUserStatus" value="Suspend">
                                <span>(o) <strong>Suspend</strong> - Akun dibekukan sementara karena peringatan/pelanggaran</span>
                            </label>
                            <label class="admin-radio-label">
                                <input type="radio" name="editUserStatus" value="Banned">
                                <span>( ) <strong>Banned</strong> - Blokir permanen akun terkait tindakan penipuan</span>
                            </label>
                        </div>
                    </div>

                    <!-- Alasan Sanksi Sesuai Wireframe -->
                    <div class="admin-form-group">
                        <label class="admin-form-label">Alasan Sanksi</label>
                        <textarea id="editUserSanksi" class="admin-form-textarea" placeholder="Tuliskan catatan alasan sanksi..."></textarea>
                        <div class="admin-form-note">(Catatan ini akan dikirimkan ke email pengguna sebagai notifikasi resmi)</div>
                    </div>
                </div>
                <div class="admin-modal-footer">
                    <button type="button" class="btn-admin-secondary" onclick="closeModal('modalEditUser')">[ Batal ]</button>
                    <button type="submit" class="btn-admin-primary">[ Simpan Perubahan ]</button>
                </div>
            </form>
        </div>
    </div>


    <!-- 3. MODAL ADMIN FORM EDIT STATUS BARANG (Sesuai Wireframe Screenshot 2026-10-29 120102) -->
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
                    <!-- Informasi Barang (Read Only) Sesuai Wireframe -->
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

                    <!-- Tindakan Admin Sesuai Wireframe: ( ) Biarkan Tersedia (o) Dihapus (Take Down) -->
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

                    <!-- Alasan Hapus Sesuai Wireframe -->
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


    <!-- 4. MODAL ADMIN DETAIL STATUS BARANG (Sesuai Wireframe Screenshot 2026-10-29 120102) -->
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

                <!-- Catatan Sistem Sesuai Wireframe -->
                <div class="admin-system-notice">
                    *Catatan Sistem: Hak akses Admin dibatasi. Admin hanya dapat memantau status transaksi tanpa bisa mengubah keputusan ACC/Tolak penjual.
                </div>
            </div>
            <div class="admin-modal-footer">
                <button type="button" class="btn-admin-primary" onclick="closeModal('modalDetailBarang')">[ Tutup Detail ]</button>
            </div>
        </div>
    </div>


    <!-- 5. MODAL ADMIN FORM TAMBAH / EDIT KATEGORI (Sesuai Wireframe Screenshot 2026-10-29 120326) -->
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


    <!-- 6. MODAL ADMIN FORM TINJAU REPORT (Sesuai Wireframe Screenshot 2026-10-29 120326) -->
    <div id="modalTinjauReport" class="admin-modal-backdrop">
        <div class="admin-modal-card" style="max-width: 640px;">
            <div class="admin-modal-header">
                <div>
                    <button type="button" class="admin-modal-back-btn" onclick="closeModal('modalTinjauReport')">&lt; Kembali ke Daftar Aduan</button>
                    <h2 class="admin-modal-title">TINJAU LAPORAN ADUAN</h2>
                </div>
                <button type="button" class="admin-modal-close" onclick="closeModal('modalTinjauReport')">&times;</button>
            </div>
            <form id="tinjauReportForm" onsubmit="handleEksekusiReportSubmit(event)">
                <input type="hidden" id="tinjauReportId">
                <div class="admin-modal-body">
                    <!-- Detail Laporan Sesuai Wireframe -->
                    <div class="admin-info-box">
                        <div id="tinjauHeaderBadge" class="admin-info-box-title" style="color: #DC2626;">Detail Laporan #90</div>
                        <div class="admin-info-row">
                            <span class="admin-info-label">Pelapor :</span>
                            <span id="tinjauPelapor" class="admin-info-value">Citra (@citra_ig)</span>
                        </div>
                        <div class="admin-info-row">
                            <span class="admin-info-label">Terlapor :</span>
                            <span id="tinjauTerlapor" class="admin-info-value" style="color: #DC2626;">Andi (@andi_ig)</span>
                        </div>
                        <div class="admin-info-row">
                            <span class="admin-info-label">Alasan Aduan :</span>
                            <span id="tinjauAlasan" class="admin-info-value">Penipuan / Barang Palsu</span>
                        </div>
                        <div class="admin-info-row" style="flex-direction: column; align-items: flex-start; gap: 4px; padding-top: 8px;">
                            <span class="admin-info-label">Deskripsi Pengaduan :</span>
                            <span id="tinjauDeskripsi" style="font-size: 13px; color: #1E293B; line-height: 1.4; font-style: italic;">
                                "Barang tidak sesuai foto dan penjual memaksa COD di tempat sepi..."
                            </span>
                        </div>
                    </div>

                    <!-- Bukti Foto Sesuai Wireframe -->
                    <div class="admin-form-group">
                        <label class="admin-form-label">Bukti Foto Pengaduan</label>
                        <div id="tinjauEvidenceGallery" class="admin-evidence-gallery">
                            <!-- Populated dynamically via JS -->
                        </div>
                    </div>

                    <!-- TINDAKAN ADMIN Sesuai Wireframe -->
                    <div class="admin-form-group" style="margin-top: 20px;">
                        <label class="admin-form-label" style="text-transform: uppercase; letter-spacing: 0.5px; color: #1D356D;">
                            ---------- TINDAKAN ADMIN ----------
                        </label>
                        <div class="admin-radio-group">
                            <label class="admin-radio-label">
                                <input type="radio" name="reportKeputusan" value="Abaikan">
                                <span>( ) <strong>Abaikan Laporan</strong> (Laporan Tidak Valid / Salah Paham)</span>
                            </label>
                            <label class="admin-radio-label">
                                <input type="radio" name="reportKeputusan" value="Suspend" checked>
                                <span>(o) <strong>Suspend Akun Terlapor</strong> (Sanksi Sementara &amp; Peringatan)</span>
                            </label>
                            <label class="admin-radio-label">
                                <input type="radio" name="reportKeputusan" value="Banned">
                                <span>( ) <strong>Banned Permanen Akun Terlapor</strong> (Sanksi Berat Penipuan)</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="admin-modal-footer">
                    <button type="button" class="btn-admin-secondary" onclick="closeModal('modalTinjauReport')">[ Batal ]</button>
                    <button type="submit" class="btn-admin-danger">[ Eksekusi Keputusan ]</button>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript Files -->
    <script>window.CURRENT_ADMIN_PAGE = 'dashboard';</script>
    <script src="<?= asset('/js/admin.js') ?>"></script>
</body>
</html>
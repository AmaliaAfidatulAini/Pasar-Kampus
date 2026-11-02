<?php
$session = get_session();
$adminName = $session['name'] ?? 'Superadmin Pusat';
$activeTab = 'report';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Aduan - Portal Superadmin Pasar Kampus</title>
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
                        <strong id="adminCurrentBreadcrumb">Laporan Aduan Masuk</strong>
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
                     VIEW 5: REPORT (Laporan Aduan Masuk)
                     (Sesuai Wireframe 'Screenshot 2026-10-29 120326.png')
                     ========================================================= -->
                <section id="view-report" class="admin-view-panel active">
                    <div class="admin-page-header">
                        <div class="admin-header-title-wrap">
                            <h1>Laporan Aduan Masuk (Report)</h1>
                            <p>Tinjau pengaduan indikasi penipuan, barang palsu, atau ghosting COD antar sesama pengguna</p>
                        </div>
                    </div>

                    <!-- Table Sesuai Wireframe: ID | Pelapor | Terlapor | Alasan | Bukti | Aksi ([Tinjau][Del]) -->
                    <div class="admin-table-card">
                        <div class="admin-table-responsive">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th style="width: 70px;">ID</th>
                                        <th>Pelapor</th>
                                        <th>Terlapor</th>
                                        <th>Alasan Aduan</th>
                                        <th>Bukti Foto</th>
                                        <th>Status Laporan</th>
                                        <th style="width: 170px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="reportTableBody">
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
         MODALS (Report Management)
         ==================================================================== -->

    <!-- MODAL ADMIN FORM TINJAU REPORT -->
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
                    <!-- Detail Laporan -->
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

                    <!-- Bukti Foto -->
                    <div class="admin-form-group">
                        <label class="admin-form-label">Bukti Foto Pengaduan</label>
                        <div id="tinjauEvidenceGallery" class="admin-evidence-gallery">
                            <!-- Populated dynamically via JS -->
                        </div>
                    </div>

                    <!-- TINDAKAN ADMIN -->
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
    <script>window.CURRENT_ADMIN_PAGE = 'report';</script>
    <script src="<?= asset('/js/admin.js') ?>"></script>
</body>
</html>

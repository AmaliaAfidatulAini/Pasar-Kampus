<?php
$session = get_session();
$adminName = $session['name'] ?? 'Superadmin Pusat';
$activeTab = 'users';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola User - Portal Superadmin Pasar Kampus</title>
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
                        <strong id="adminCurrentBreadcrumb">Kelola Pengguna (User)</strong>
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
                     VIEW 2: CRUD ADMIN USER (Kelola User)
                     (Sesuai Wireframe 'Screenshot 2026-10-29 114058.png')
                     ========================================================= -->
                <section id="view-users" class="admin-view-panel active">
                    <div class="admin-page-header">
                        <div class="admin-header-title-wrap">
                            <h1>Manajemen Pengguna (User)</h1>
                            <p>Kelola data mahasiswa terdaftar, hak akses, serta penegakan sanksi di seluruh kampus</p>
                        </div>
                        <!-- Tombol Tambah Pengguna Sesuai Wireframe [+ Tambah] -->
                        <button type="button" class="btn-admin-primary" onclick="openAddUserModal()">
                            <span>+ Tambah Pengguna</span>
                        </button>
                    </div>

                    <!-- Filter Toolbar -->
                    <div class="admin-table-card">
                        <div class="admin-table-toolbar">
                            <div class="admin-search-wrap">
                                <span class="admin-search-icon">🔍</span>
                                <input type="text" id="userSearchInput" class="admin-search-input" placeholder="Cari nama, NIM, atau ID..." oninput="renderUsersTable()">
                            </div>
                            <div class="admin-toolbar-actions">
                                <select id="userFilterKampus" class="admin-select-filter" onchange="renderUsersTable()">
                                    <option value="all">Semua Kampus</option>
                                    <option value="Brawijaya">Univ. Brawijaya (UB)</option>
                                    <option value="Negeri Malang">Univ. Negeri Malang (UM)</option>
                                    <option value="Muhammadiyah">Univ. Muhammadiyah Malang (UMM)</option>
                                </select>
                                <select id="userFilterStatus" class="admin-select-filter" onchange="renderUsersTable()">
                                    <option value="all">Semua Status</option>
                                    <option value="Aktif">Aktif</option>
                                    <option value="Suspend">Suspend</option>
                                    <option value="Banned">Banned</option>
                                </select>
                            </div>
                        </div>

                        <!-- User Table Sesuai Wireframe: ID | Nama Lengkap | NIM | Kampus | Status | Aksi -->
                        <div class="admin-table-responsive">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th style="width: 70px;">ID</th>
                                        <th>Nama Lengkap & Email</th>
                                        <th>NIM</th>
                                        <th>Kampus</th>
                                        <th>Status</th>
                                        <th style="width: 190px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="userTableBody">
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
         MODALS (Users Management)
         ==================================================================== -->

    <!-- 1. MODAL ADMIN FORM TAMBAH USER -->
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

    <!-- 2. MODAL ADMIN FORM EDIT STATUS USER -->
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
                    <!-- Informasi Pengguna (Read Only) -->
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

                    <!-- Ubah Status -->
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

                    <!-- Alasan Sanksi -->
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

    <!-- JavaScript Files -->
    <script>window.CURRENT_ADMIN_PAGE = 'users';</script>
    <script src="<?= asset('/js/admin.js') ?>"></script>
</body>
</html>

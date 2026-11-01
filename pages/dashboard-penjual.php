<?php
$session = get_session();
$userName = $session['name'] ?? 'Penjual';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Penjual - Pasar Kampus</title>
    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- CSS Stylesheet -->
    <link rel="stylesheet" href="<?= route('/css/style.css') ?>">
</head>
<body class="seller-body">

    <!-- Top Navbar Penjual -->
    <header class="seller-navbar">
        <div class="seller-nav-left">
            <a href="<?= route('/') ?>" class="seller-brand">
                <span>PasarKampus</span>
                <span class="seller-badge-penjual">Penjual</span>
            </a>
            <ul class="seller-nav-links">
                <li><a id="navTabDashboard" class="seller-nav-link active" onclick="switchSellerView('dashboard')">Dashboard</a></li>
                <li><a href="<?= route('/dashboard.php') ?>" class="seller-nav-link">Katalog</a></li>
                <li><a id="navTabPengajuan" class="seller-nav-link" onclick="switchSellerView('all-pengajuan')">Pengajuan Masuk</a></li>
                <li><a href="<?= route('/riwayat.php') ?>" class="seller-nav-link">Riwayat Saya</a></li>
            </ul>
        </div>

        <div class="seller-nav-right">
            <div class="seller-user-info" style="cursor: pointer;" onclick="window.location.href='<?= route('/profil.php') ?>'" title="Buka Profil Pengguna">
                <img id="sellerUserAvatar" src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&q=80&w=150" alt="Avatar" class="seller-avatar">
                <span id="sellerUserName"><?= esc($userName) ?> (Penjual)</span>
            </div>
            <a href="<?= route('/logout.php') ?>" class="btn-logout">Logout</a>
        </div>
    </header>

    <!-- Main Seller Container -->
    <main class="seller-container">

        <!-- ================= VIEW 1: DAFTAR BARANG JUALAN SAYA ================= -->
        <section id="viewBarangSaya" class="seller-view-panel active">
            
            <!-- Quick Stat Cards -->
            <div class="seller-stats-grid">
                <div class="seller-stat-card">
                    <div class="seller-stat-icon" style="background: #EEF2FF; color: #1D356D;">📦</div>
                    <div class="seller-stat-info">
                        <h3 id="statTotalBarang">0</h3>
                        <p>Total Barang Jualan</p>
                    </div>
                </div>
                <div class="seller-stat-card">
                    <div class="seller-stat-icon" style="background: #DCFCE7; color: #15803D;">✅</div>
                    <div class="seller-stat-info">
                        <h3 id="statTersedia">0</h3>
                        <p>Barang Tersedia</p>
                    </div>
                </div>
                <div class="seller-stat-card">
                    <div class="seller-stat-icon" style="background: #FEF3C7; color: #B45309;">⏳</div>
                    <div class="seller-stat-info">
                        <h3 id="statProcess">0</h3>
                        <p>Sedang Diproses</p>
                    </div>
                </div>
            </div>

            <!-- Card Barang Jualan Saya -->
            <div class="seller-card">
                <div class="seller-card-header">
                    <div>
                        <h1 class="seller-card-title">Barang Jualan Saya</h1>
                        <p class="seller-card-subtitle">Daftar barang preloved milik Anda yang ditayangkan di Pasar Kampus</p>
                    </div>
                    <button type="button" onclick="openAddModal()" class="btn-pbl-primary">
                        <span>+ Tambah Barang</span>
                    </button>
                </div>

                <!-- Table Daftar Barang -->
                <div class="seller-table-wrapper">
                    <table class="seller-table">
                        <thead>
                            <tr>
                                <th style="width: 50px;">ID</th>
                                <th style="width: 80px;">Foto</th>
                                <th>Nama Barang</th>
                                <th>Harga</th>
                                <th>Status</th>
                                <th style="width: 220px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="sellerProductTableBody">
                            <!-- Populated dynamically via JS -->
                        </tbody>
                    </table>
                </div>

                <div id="sellerProductEmpty" class="seller-empty-state" style="display: none;">
                    <div class="seller-empty-icon">📭</div>
                    <p style="font-size: 1rem; font-weight: 700; color: var(--pbl-navy); margin-bottom: 4px;">Belum Ada Barang Jualan</p>
                    <p style="margin-top: 0; font-size: 0.85rem;">Mulai jual barang kuliah yang sudah tidak terpakai sekarang!</p>
                    <button type="button" onclick="openAddModal()" class="btn-pbl-primary" style="margin-top: 10px;">
                        + Tambah Barang Baru
                    </button>
                </div>
            </div>
        </section>

        <!-- ================= VIEW 2: DETAIL PENGAJUAN MASUK ================= -->
        <section id="viewDetailPengajuan" class="seller-view-panel">
            <div class="seller-card">
                <div class="seller-card-header">
                    <div>
                        <h2 id="pengajuanCardTitle" class="seller-card-title">Daftar Pengajuan untuk: -</h2>
                        <p class="seller-card-subtitle">Mahasiswa yang mengajukan pembelian untuk barang ini</p>
                    </div>
                    <button type="button" onclick="switchSellerView('dashboard')" class="btn-pbl-outline">
                        ← Kembali ke Barang Saya
                    </button>
                </div>

                <div class="seller-table-wrapper">
                    <table class="seller-table">
                        <thead>
                            <tr>
                                <th>Pembeli</th>
                                <th>Kampus</th>
                                <th>Waktu Pengajuan</th>
                                <th>Status</th>
                                <th style="width: 180px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="pengajuanTableBody"></tbody>
                    </table>
                </div>

                <div id="pengajuanEmpty" class="seller-empty-state" style="display: none;">
                    <div class="seller-empty-icon">🤝</div>
                    <p style="font-size: 1rem; font-weight: 700; color: var(--pbl-navy); margin-bottom: 4px;">Belum Ada Pengajuan Masuk</p>
                    <p style="margin-top: 0; font-size: 0.85rem;">Belum ada mahasiswa yang mengajukan pembelian untuk barang ini.</p>
                </div>
            </div>
        </section>

        <!-- ================= VIEW 3: SEMUA PENGAJUAN MASUK ================= -->
        <section id="viewAllPengajuan" class="seller-view-panel">
            <div class="seller-card">
                <div class="seller-card-header">
                    <div>
                        <h2 class="seller-card-title">Semua Pengajuan Masuk</h2>
                        <p class="seller-card-subtitle">Seluruh pengajuan transaksi pembelian dari mahasiswa untuk barang-barang Anda</p>
                    </div>
                    <button type="button" onclick="switchSellerView('dashboard')" class="btn-pbl-outline">
                        ← Kembali ke Dashboard
                    </button>
                </div>

                <div class="seller-table-wrapper">
                    <table class="seller-table">
                        <thead>
                            <tr>
                                <th>Barang</th>
                                <th>Pembeli</th>
                                <th>Kampus</th>
                                <th>Waktu Pengajuan</th>
                                <th>Status</th>
                                <th style="width: 180px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="allPengajuanTableBody"></tbody>
                    </table>
                </div>

                <div id="allPengajuanEmpty" class="seller-empty-state" style="display: none;">
                    <div class="seller-empty-icon">📬</div>
                    <p style="font-size: 1rem; font-weight: 700; color: var(--pbl-navy);">Belum ada pengajuan masuk untuk toko Anda.</p>
                </div>
            </div>
        </section>

    </main>

    <!-- ================= MODAL: FORM TAMBAH / EDIT BARANG ================= -->
    <div id="sellerProductModal" class="seller-modal-overlay">
        <div class="seller-modal-card">
            <div class="seller-modal-header">
                <h3 id="modalFormTitle" class="seller-modal-title">Tambah Barang Baru</h3>
                <button type="button" onclick="closeProductModal()" class="seller-modal-close">&times;</button>
            </div>

            <form id="sellerProductForm" onsubmit="handleSaveProduct(event)">
                <input type="hidden" id="editProductId" value="">

                <div class="seller-form-group">
                    <label class="seller-form-label" for="prodName">Nama Barang <span style="color:#ef4444;">*</span></label>
                    <input type="text" id="prodName" required class="seller-input" placeholder="Contoh: Buku Kalkulus Purcell Edisi 9">
                </div>

                <div class="seller-form-group">
                    <label class="seller-form-label" for="prodDesc">Deskripsi</label>
                    <textarea id="prodDesc" rows="3" class="seller-textarea" placeholder="Jelaskan kondisi detail barang, kelengkapan, dll."></textarea>
                </div>

                <div class="seller-form-group">
                    <label class="seller-form-label" for="prodPrice">Harga (Rp) <span style="color:#ef4444;">*</span></label>
                    <input type="number" id="prodPrice" required min="1000" step="1000" class="seller-input" placeholder="Contoh: 50000">
                </div>

                <div class="seller-form-group">
                    <label class="seller-form-label">Kondisi <span style="color:#ef4444;">*</span></label>
                    <div class="seller-radio-group">
                        <label class="seller-radio-label">
                            <input type="radio" name="prodCondition" value="Sangat Bagus" checked>
                            <span>Sangat Bagus</span>
                        </label>
                        <label class="seller-radio-label">
                            <input type="radio" name="prodCondition" value="Baik">
                            <span>Baik</span>
                        </label>
                        <label class="seller-radio-label">
                            <input type="radio" name="prodCondition" value="Cukup">
                            <span>Cukup</span>
                        </label>
                    </div>
                </div>

                <div class="seller-form-group">
                    <label class="seller-form-label" for="prodCategory">Kategori <span style="color:#ef4444;">*</span></label>
                    <select id="prodCategory" required class="seller-select">
                        <option value="" disabled selected>Pilih Kategori</option>
                        <option value="Buku">Buku</option>
                        <option value="Elektronik">Elektronik</option>
                        <option value="ATK">ATK</option>
                        <option value="Perlengkapan Kost">Perlengkapan Kost</option>
                        <option value="Pakaian">Pakaian</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <div class="seller-form-group">
                    <label class="seller-form-label">Foto Barang</label>
                    <div class="seller-upload-box" onclick="document.getElementById('prodPhotoInput').click()">
                        <input type="file" id="prodPhotoInput" accept="image/*" style="display: none;" onchange="handleProductPhoto(event)">
                        <div id="uploadPlaceholder">
                            <div style="font-size: 2rem; margin-bottom: 4px;">📷</div>
                            <span style="font-size: 0.85rem; font-weight: 600; color: var(--pbl-navy);">Klik untuk upload foto barang</span>
                        </div>
                        <img id="photoPreviewImg" src="" alt="Preview" style="display: none; max-height: 120px; border-radius: 8px; margin: 0 auto;">
                    </div>
                </div>

                <div class="seller-modal-footer">
                    <button type="button" onclick="closeProductModal()" class="btn-pbl-outline">Batal</button>
                    <button type="submit" class="btn-pbl-primary">Simpan Barang</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ================= FOOTER ================= -->
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
                    &copy; <?= date('Y') ?> PasarKampus. Hak Cipta Dilindungi.
                </div>
                <div>
                    Dibuat untuk Project Based Learning (PBL) Mahasiswa.
                </div>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="<?= route('/js/dashboard-penjual.js') ?>"></script>
</body>
</html>

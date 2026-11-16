<?php
$session = session_include('/login');
$userId = (int) ($session['user_id'] ?? 0);

if ($userId <= 0) {
    redirect('/login', 302);
    exit;
}

// Handle Actions (Terima, Tolak, Batalkan, Selesaikan, Hapus)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $txId = (int) ($_POST['transaction_id'] ?? 0);
    $barangId = (int) ($_POST['barang_id'] ?? $_POST['product_id'] ?? 0);

    if ($action === 'terima_pengajuan' && $txId && $userId) {
        $req = executeQuery(
            "SELECT r.transaction_id, r.product_id, r.user_id 
             FROM requests r 
             JOIN products p ON p.product_id = r.product_id 
             WHERE r.transaction_id = :id AND p.user_id = :uid", 
            ['id' => $txId, 'uid' => $userId], 
            'fetch'
        );
        if ($req) {
            $pid = (int) $req['product_id'];
            executeQuery("UPDATE requests SET status = 'PROCESS', updated_at = CURRENT_TIMESTAMP WHERE transaction_id = :id", ['id' => $txId]);
            executeQuery("UPDATE products SET status = 'Proses', updated_at = CURRENT_TIMESTAMP WHERE product_id = :pid AND user_id = :uid", ['pid' => $pid, 'uid' => $userId]);
            // Reject other pending requests for this product
            executeQuery("UPDATE requests SET status = 'CANCELLED', updated_at = CURRENT_TIMESTAMP WHERE product_id = :pid AND transaction_id != :id AND status = 'PENDING'", ['pid' => $pid, 'id' => $txId]);
        }
    } elseif ($action === 'tolak_pengajuan' && $txId && $userId) {
        $req = executeQuery(
            "SELECT r.transaction_id, r.product_id 
             FROM requests r 
             JOIN products p ON p.product_id = r.product_id 
             WHERE r.transaction_id = :id AND p.user_id = :uid", 
            ['id' => $txId, 'uid' => $userId], 
            'fetch'
        );
        if ($req) {
            executeQuery("UPDATE requests SET status = 'CANCELLED', updated_at = CURRENT_TIMESTAMP WHERE transaction_id = :id", ['id' => $txId]);
        }
    } elseif ($action === 'batalkan_pengajuan_modal' && $txId && $userId) {
        $req = executeQuery(
            "SELECT r.transaction_id, r.product_id 
             FROM requests r 
             JOIN products p ON p.product_id = r.product_id 
             WHERE r.transaction_id = :id AND p.user_id = :uid", 
            ['id' => $txId, 'uid' => $userId], 
            'fetch'
        );
        if ($req) {
            $pid = (int) $req['product_id'];
            executeQuery("UPDATE requests SET status = 'CANCELLED', updated_at = CURRENT_TIMESTAMP WHERE transaction_id = :id", ['id' => $txId]);
            $stillProcess = executeQuery("SELECT transaction_id FROM requests WHERE product_id = :pid AND status = 'PROCESS'", ['pid' => $pid], 'fetch');
            if (!$stillProcess) {
                executeQuery("UPDATE products SET status = 'Tersedia', updated_at = CURRENT_TIMESTAMP WHERE product_id = :pid AND user_id = :uid", ['pid' => $pid, 'uid' => $userId]);
            }
        }
    } elseif ($action === 'batalkan_proses' && $barangId && $userId) {
        $p = executeQuery("SELECT product_id FROM products WHERE product_id = :pid AND user_id = :uid", ['pid' => $barangId, 'uid' => $userId], 'fetch');
        if ($p) {
            executeQuery("UPDATE products SET status = 'Tersedia', updated_at = CURRENT_TIMESTAMP WHERE product_id = :pid AND user_id = :uid", ['pid' => $barangId, 'uid' => $userId]);
            executeQuery("UPDATE requests SET status = 'CANCELLED', updated_at = CURRENT_TIMESTAMP WHERE product_id = :pid AND status = 'PROCESS'", ['pid' => $barangId]);
        }
    } elseif ($action === 'selesaikan_transaksi' && $barangId && $userId) {
        $p = executeQuery("SELECT product_id FROM products WHERE product_id = :pid AND user_id = :uid", ['pid' => $barangId, 'uid' => $userId], 'fetch');
        if ($p) {
            executeQuery("UPDATE products SET status = 'Terjual', updated_at = CURRENT_TIMESTAMP WHERE product_id = :pid AND user_id = :uid", ['pid' => $barangId, 'uid' => $userId]);
            executeQuery("UPDATE requests SET status = 'COMPLETED', completed_date = CURRENT_DATE, updated_at = CURRENT_TIMESTAMP WHERE product_id = :pid AND status = 'PROCESS'", ['pid' => $barangId]);
        }
    } elseif ($action === 'hapus_barang' && $barangId && $userId) {
        $p = executeQuery("SELECT product_id FROM products WHERE product_id = :pid AND user_id = :uid", ['pid' => $barangId, 'uid' => $userId], 'fetch');
        if ($p) {
            executeQuery("UPDATE products SET status = 'Dihapus', updated_at = CURRENT_TIMESTAMP WHERE product_id = :pid AND user_id = :uid", ['pid' => $barangId, 'uid' => $userId]);
            executeQuery("UPDATE requests SET status = 'CANCELLED', updated_at = CURRENT_TIMESTAMP WHERE product_id = :pid AND status IN ('PENDING', 'PROCESS')", ['pid' => $barangId]);
        }
    }

    if (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json')) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }
    redirect('/dashboard-penjual.php', 302);
    exit;
}

$sellerProductsDb = [];
if ($userId) {
    $sellerProductsDb = executeQuery(
        "SELECT p.product_id AS id,
                p.item_name AS \"namaBarang\",
                p.price AS harga,
                COALESCE(c.category_name, 'Lainnya') AS kategori,
                p.status,
                COALESCE(p.moderation_note, '') AS \"alasanAdmin\",
                TO_CHAR(p.updated_at, 'DD Mon YYYY') AS \"tglUpdate\",
                COALESCE(a.file_path, 'https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?w=300') AS gambar
         FROM products p
         LEFT JOIN categories c ON c.categories_id = p.category_id
         LEFT JOIN LATERAL (
             SELECT file_path
             FROM attachments
             WHERE product_id = p.product_id
             ORDER BY attachment_type ASC, attachment_id ASC
             LIMIT 1
         ) a ON TRUE
         WHERE p.user_id = :user_id AND p.status != 'Dihapus'
         ORDER BY p.product_id DESC",
        ['user_id' => $userId],
        'fetchAll'
    );

    foreach ($sellerProductsDb as &$sp) {
        $sp['id'] = (int) $sp['id'];
        $sp['harga'] = (int) $sp['harga'];
        $sp['tglTerjual'] = $sp['tglUpdate'];
        $sp['tglDitolak'] = $sp['tglUpdate'];
        $reqs = executeQuery(
            "SELECT r.transaction_id AS id,
                    COALESCE(up.name, 'Pembeli') AS pembeli,
                    COALESCE(up.campus, 'Kampus') AS kampus,
                    COALESCE(up.phone, '081234567890') AS wa,
                    TO_CHAR(r.created_at, 'DD Month YYYY') AS waktu,
                    CASE 
                        WHEN r.status = 'PROCESS' THEN 'Diterima'
                        WHEN r.status = 'CANCELLED' THEN 'Ditolak'
                        WHEN r.status = 'COMPLETED' THEN 'Selesai'
                        ELSE 'Pending'
                    END AS status
             FROM requests r
             LEFT JOIN user_profiles up ON up.user_id = r.user_id
             WHERE r.product_id = :product_id
             ORDER BY r.transaction_id ASC",
            ['product_id' => $sp['id']],
            'fetchAll'
        );
        foreach ($reqs as &$rq) {
            $rq['id'] = (int) $rq['id'];
        }
        unset($rq);
        $sp['pengajuanList'] = $reqs;
        if ($sp['status'] === 'Proses') {
            $accepted = array_filter($reqs, fn($r) => $r['status'] === 'Diterima');
            $first = reset($accepted);
            $sp['pembeliDiterima'] = $first ? $first['pembeli'] : 'Pembeli';
            $sp['pembeliWa'] = $first ? $first['wa'] : '081987654321';
        }
    }
    unset($sp);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PasarKampus - Dashboard Penjual</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --pk-navy: #1D356D;
            --pk-amber: #F59E0B;
            --pk-green: #22C55E;
            --pk-red: #EF4444;
            --pk-bg: #F8FAFC;
        }
        body {
            background-color: var(--pk-bg);
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0; padding: 0; color: #1E293B;
        }
        .pk-navbar {
            background: linear-gradient(180deg, #1D4ED8 0%, #2355B8 60%, #1E40AF 100%);
            height: 64px; display: flex; align-items: center; padding: 0 28px; color: #FFF;
        }
        .pk-nav-inner {
            width: 100%; max-width: 1200px; margin: 0 auto;
            display: flex; align-items: center; justify-content: space-between;
        }
        .pk-nav-logo { font-size: 1.35rem; font-weight: 800; color: #FFF; text-decoration: none; }
        .pk-nav-links a { color: #FFF; text-decoration: none; margin-left: 20px; font-weight: 600; font-size: 0.9rem; }
        
        .pk-container { max-width: 1060px; margin: 32px auto 60px; padding: 0 20px; }
        .pk-page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .pk-page-title { font-size: 1.6rem; font-weight: 800; color: var(--pk-navy); margin: 0; }
        
        .btn-tambah-barang {
            background: var(--pk-navy); color: #FFF; padding: 10px 22px;
            border-radius: 9999px; font-weight: 700; text-decoration: none; font-size: 0.9rem;
        }

        /* Filter Toolbar (Poin 5) */
        .filter-toolbar {
            display: flex; gap: 12px; margin-bottom: 24px; flex-wrap: wrap; background: #FFF;
            padding: 16px; border-radius: 14px; border: 1px solid #E2E8F0; align-items: center;
        }
        .filter-select, .filter-search {
            padding: 8px 14px; border: 1px solid #CBD5E1; border-radius: 8px; font-family: inherit; font-size: 0.85rem; outline: none;
        }
        .filter-search { flex: 1; min-width: 200px; }

        /* Card List barang */
        .barang-card-list { display: flex; flex-direction: column; gap: 18px; }
        .item-card {
            background: #FFF; border-radius: 16px; border: 1px solid #E2E8F0;
            padding: 20px; display: flex; gap: 20px; align-items: center; position: relative;
        }
        .item-img { width: 110px; height: 110px; border-radius: 12px; object-fit: cover; }
        .item-info { flex: 1; }
        .item-title { font-size: 1.1rem; font-weight: 800; color: var(--pk-navy); margin: 0 0 8px 0; }
        .item-meta { font-size: 0.88rem; color: #475569; display: flex; flex-direction: column; gap: 4px; }
        .item-status-badge { position: absolute; top: 20px; right: 20px; }

        /* Status Badges */
        .badge-tersedia { background: #DCFCE7; color: #15803D; padding: 4px 12px; border-radius: 6px; font-weight: 700; font-size: 0.78rem; }
        .badge-proses { background: #FEF3C7; color: #D97706; padding: 4px 12px; border-radius: 6px; font-weight: 700; font-size: 0.78rem; }
        .badge-ditolak { background: #FEE2E2; color: #B91C1C; padding: 4px 12px; border-radius: 6px; font-weight: 700; font-size: 0.78rem; }
        .badge-terjual { background: #F1F5F9; color: #64748B; padding: 4px 12px; border-radius: 6px; font-weight: 700; font-size: 0.78rem; }

        /* Action Buttons */
        .action-btns { display: flex; gap: 10px; margin-top: 14px; }
        .btn-cek { background: var(--pk-navy); color: #FFF; border: none; padding: 7px 16px; border-radius: 6px; font-weight: 700; font-size: 0.8rem; cursor: pointer; }
        .btn-hapus { background: #FEE2E2; color: #B91C1C; border: none; padding: 7px 16px; border-radius: 6px; font-weight: 700; font-size: 0.8rem; cursor: pointer; }
        .btn-wa { background: #22C55E; color: #FFF; text-decoration: none; padding: 7px 16px; border-radius: 6px; font-weight: 700; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 6px; }
        .btn-selesai { background: #2563EB; color: #FFF; border: none; padding: 7px 16px; border-radius: 6px; font-weight: 700; font-size: 0.8rem; cursor: pointer; }
        .btn-batal-proses { background: #E2E8F0; color: #475569; border: none; padding: 7px 16px; border-radius: 6px; font-weight: 700; font-size: 0.8rem; cursor: pointer; }

        /* Modal Styles */
        .pk-modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.45); backdrop-filter: blur(4px);
            display: flex; align-items: center; justify-content: center;
            z-index: 1000; opacity: 0; visibility: hidden; transition: 0.25s; padding: 20px; box-sizing: border-box;
        }
        .pk-modal-overlay.active { opacity: 1; visibility: visible; }
        .pk-modal-container {
            background: #FFF; border-radius: 20px; width: 100%; max-width: 680px; padding: 32px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.12); position: relative;
        }
        .pk-modal-close { position: absolute; top: 20px; right: 20px; background: none; border: none; color: #EF4444; font-size: 1.5rem; cursor: pointer; }
        .modal-table { width: 100%; border-collapse: collapse; margin-top: 16px; font-size: 0.88rem; }
        .modal-table th { background: var(--pk-navy); color: #FFF; padding: 12px; text-align: left; }
        .modal-table td { padding: 12px; border-bottom: 1px solid #E2E8F0; }
        .btn-m-terima { background: var(--pk-navy); color: #FFF; border: none; padding: 5px 14px; border-radius: 6px; font-weight: 700; cursor: pointer; }
        .btn-m-tolak { background: #FFF; color: #EF4444; border: 1px solid #EF4444; padding: 5px 14px; border-radius: 6px; font-weight: 700; cursor: pointer; }
        .btn-m-batal { background: #FFF; color: #EF4444; border: 1px solid #EF4444; padding: 5px 14px; border-radius: 6px; font-weight: 700; cursor: pointer; }
    </style>
</head>
<body>

    <header class="pk-navbar">
        <div class="pk-nav-inner">
            <a href="<?= route('/dashboard-utama.php') ?>" class="pk-nav-logo">PasarKampus</a>
            <div class="pk-nav-links">
                <a href="<?= route('/riwayat.php') ?>">Riwayat</a>
                <a href="<?= route('/profil.php') ?>">Profil</a>
            </div>
        </div>
    </header>

    <div class="pk-container">
        <div class="pk-page-header">
            <h1 class="pk-page-title">Barang Jualan Saya</h1>
            <a href="tambah-barang.php" class="btn-tambah-barang">Tambah barang</a>
        </div>

        <!-- Filter Bar Toolbar -->
        <div class="filter-toolbar">
            <select id="filterStatus" class="filter-select" onchange="renderBarangList()">
                <option value="Semua">Semua Status</option>
                <option value="Tersedia">Tersedia</option>
                <option value="Proses">Proses</option>
                <option value="Ditolak">Ditolak / Dihapus Admin</option>
                <option value="Terjual">Terjual</option>
            </select>

            <select id="filterKategori" class="filter-select" onchange="renderBarangList()">
                <option value="Semua">Semua Kategori</option>
                <option value="Buku">Buku</option>
                <option value="Elektronik">Elektronik</option>
                <option value="Pakaian">Pakaian</option>
                <option value="Perlengkapan Kos">Perlengkapan Kos</option>
                <option value="ATK">ATK</option>
                <option value="Lainnya">Lainnya</option>
            </select>

            <input type="text" id="filterSearch" class="filter-search" placeholder="Cari nama barang..." oninput="renderBarangList()">
        </div>

        <!-- Dynamic Card Container -->
        <div class="barang-card-list" id="barangCardList">
            <!-- Data dimuat via JavaScript -->
        </div>
    </div>

    <!-- Modal Cek Pengajuan -->
    <div id="pengajuanModal" class="pk-modal-overlay" onclick="if(event.target.id==='pengajuanModal') closePengajuanModal()">
        <div class="pk-modal-container">
            <button class="pk-modal-close" onclick="closePengajuanModal()">&times;</button>
            <h2 id="modalBarangTitle" style="font-size: 1.25rem; font-weight: 800; color: var(--pk-navy); margin: 0 0 16px 0;">Daftar Pengajuan</h2>
            
            <table class="modal-table">
                <thead>
                    <tr>
                        <th>Pembeli</th>
                        <th>Kampus</th>
                        <th>Waktu Pengajuan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="pengajuanTableBody">
                    <!-- Dynamic Data Pengajuan -->
                </tbody>
            </table>
        </div>
    </div>

    <script>
        let dbSellerProducts = <?= json_encode($sellerProductsDb, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?> || [];

        // Always read from database data directly. Never use localStorage so different users are never cross-polluted!
        function getPenjualData() {
            return dbSellerProducts || [];
        }

        function savePenjualData(data) {
            dbSellerProducts = data;
        }

        function formatRupiah(num) {
            return new Intl.NumberFormat('id-ID').format(num);
        }

        let activeModalBarangId = null;

        function renderBarangList() {
            const list = getPenjualData();
            const fStatus = document.getElementById('filterStatus').value;
            const fKat = document.getElementById('filterKategori').value;
            const fSearch = document.getElementById('filterSearch').value.toLowerCase();

            const container = document.getElementById('barangCardList');
            if (list.length === 0) {
                container.innerHTML = `
                    <div style="text-align:center; padding: 48px 20px; background: #FFF; border-radius: 12px; border: 1px dashed var(--pk-border); margin: 20px 0;">
                        <div style="font-size: 2.2rem; margin-bottom: 8px;">📦</div>
                        <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--pk-navy); margin-bottom: 6px;">Belum Ada Barang Jualan</h3>
                        <p style="font-size: 0.85rem; color: #64748B; margin-bottom: 16px;">Anda belum mengunggah barang jualan apapun. Klik tombol "Tambah Barang Baru" untuk mulai menjual barang bekas kampus Anda.</p>
                        <a href="<?= route('/tambah-barang.php') ?>" class="btn-tambah" style="display: inline-flex; align-items: center; justify-content: center; text-decoration: none;">+ Tambah Barang Baru</a>
                    </div>
                `;
                return;
            }

            const filtered = list.filter(item => {
                const matchStatus = (fStatus === 'Semua') || (item.status === fStatus);
                const matchKat = (fKat === 'Semua') || (item.kategori === fKat);
                const matchSearch = (item.namaBarang || '').toLowerCase().includes(fSearch);
                return matchStatus && matchKat && matchSearch;
            });

            if (filtered.length === 0) {
                container.innerHTML = `<div style="text-align:center; padding: 40px; color: #94A3B8;">Tidak ada barang jualan yang sesuai filter pencarian.</div>`;
                return;
            }

            container.innerHTML = filtered.map(item => {
                let badgeHTML = '';
                let metaHTML = '';
                let actionHTML = '';

                // BARIS 1: STATUS TERSEDIA
                if (item.status === 'Tersedia') {
                    const pendingCount = (item.pengajuanList || []).filter(p => p.status === 'Pending').length;
                    badgeHTML = `<span class="badge-tersedia">Tersedia</span>`;
                    metaHTML = `
                        <div>Harga: Rp. ${formatRupiah(item.harga)}</div>
                        <div>Kategori: ${item.kategori}</div>
                        <div>Pengajuan Masuk: ${pendingCount} Orang menunggu</div>
                    `;
                    actionHTML = `
                        <button class="btn-cek" onclick="openPengajuanModal(${item.id})">Cek pengajuan</button>
                        <button class="btn-hapus" onclick="hapusBarang(${item.id})">Hapus</button>
                    `;
                } 
                // BARIS 2: STATUS PROSES (Penjual ACC Pengajuan)
                else if (item.status === 'Proses') {
                    badgeHTML = `<span class="badge-proses">Proses</span>`;
                    metaHTML = `
                        <div>Harga: Rp. ${formatRupiah(item.harga)}</div>
                        <div>Kategori: ${item.kategori}</div>
                        <div>Pembeli: <strong>${item.pembeliDiterima || 'Pembeli'}</strong></div>
                    `;
                    const waNum = (item.pembeliWa || '6281234567890').replace(/^0/, '62');
                    actionHTML = `
                        <a href="https://wa.me/${waNum}" target="_blank" class="btn-wa">🟢 Hubungi pembeli</a>
                        <button class="btn-selesai" onclick="selesaikanTransaksi(${item.id})">Selesaikan</button>
                        <button class="btn-batal-proses" onclick="batalkanProses(${item.id})">Batalkan</button>
                    `;
                } 
                // BARIS 3: STATUS DITOLAK / DIHAPUS ADMIN
                else if (item.status === 'Ditolak') {
                    badgeHTML = `<span class="badge-ditolak">Ditolak</span>`;
                    metaHTML = `
                        <div>Harga: Rp. ${formatRupiah(item.harga)}</div>
                        <div>Kategori: ${item.kategori}</div>
                        <div style="color: #EF4444;">Status: ${item.alasanAdmin || 'Ditolak / Dihapus Admin pada ' + (item.tglDitolak || '19 Okt 2026')}</div>
                    `;
                    actionHTML = `<button class="btn-hapus" onclick="hapusBarang(${item.id})">Hapus dari daftar</button>`;
                } 
                // BARIS 4: STATUS TERJUAL (Selesai COD)
                else if (item.status === 'Terjual') {
                    badgeHTML = `<span class="badge-terjual">Terjual</span>`;
                    metaHTML = `
                        <div>Harga: Rp. ${formatRupiah(item.harga)}</div>
                        <div>Kategori: ${item.kategori}</div>
                        <div>Terjual pada: ${item.tglTerjual || '20 Okt 2026'}</div>
                    `;
                    actionHTML = `<span style="font-size: 0.82rem; color: #64748B;">Transaksi selesai</span>`;
                }

                return `
                    <div class="item-card">
                        <img src="${item.gambar}" class="item-img" alt="Foto Produk">
                        <div class="item-info">
                            <h3 class="item-title">${item.namaBarang}</h3>
                            <div class="item-meta">${metaHTML}</div>
                            <div class="action-btns">${actionHTML}</div>
                        </div>
                        <div class="item-status-badge">Status: ${badgeHTML}</div>
                    </div>
                `;
            }).join('');
        }

        /* Logic Pop-up Modal Cek Pengajuan */
        function openPengajuanModal(barangId) {
            activeModalBarangId = barangId;
            const list = getPenjualData();
            const item = list.find(b => b.id === barangId);
            if (!item) return;

            document.getElementById('modalBarangTitle').innerText = `Daftar Pengajuan : ${item.namaBarang}`;
            renderPengajuanModalTable(item);

            document.getElementById('pengajuanModal').classList.add('active');
        }

        function renderPengajuanModalTable(item) {
            const tbody = document.getElementById('pengajuanTableBody');
            if (!item.pengajuanList || item.pengajuanList.length === 0) {
                tbody.innerHTML = `<tr><td colspan="5" style="text-align:center; color:#94A3B8;">Belum ada pengajuan masuk.</td></tr>`;
                return;
            }

            tbody.innerHTML = item.pengajuanList.map(p => {
                let statusSpan = '';
                let btnAksi = '';

                if (p.status === 'Pending') {
                    statusSpan = `<span style="background:#FEF3C7; color:#D97706; padding:3px 8px; border-radius:4px; font-weight:700;">Pending</span>`;
                    btnAksi = `
                        <button class="btn-m-terima" onclick="terimaPengajuan(${p.id})">Terima</button>
                        <button class="btn-m-tolak" onclick="tolakPengajuan(${p.id})">Tolak</button>
                    `;
                } else if (p.status === 'Diterima') {
                    statusSpan = `<span style="background:#DCFCE7; color:#15803D; padding:3px 8px; border-radius:4px; font-weight:700;">Diterima</span>`;
                    btnAksi = `<button class="btn-m-batal" onclick="batalkanPengajuanInModal(${p.id})">Batalkan</button>`;
                } else if (p.status === 'Ditolak') {
                    statusSpan = `<span style="background:#FEE2E2; color:#B91C1C; padding:3px 8px; border-radius:4px; font-weight:700;">Ditolak</span>`;
                    btnAksi = `-`;
                } else if (p.status === 'Selesai') {
                    statusSpan = `<span style="background:#E2E8F0; color:#334155; padding:3px 8px; border-radius:4px; font-weight:700;">Selesai</span>`;
                    btnAksi = `-`;
                }

                return `
                    <tr>
                        <td><strong>${p.pembeli}</strong></td>
                        <td>${p.kampus}</td>
                        <td>${p.waktu}</td>
                        <td>${statusSpan}</td>
                        <td>${btnAksi}</td>
                    </tr>
                `;
            }).join('');
        }

        function closePengajuanModal() {
            document.getElementById('pengajuanModal').classList.remove('active');
            activeModalBarangId = null;
        }

        /* Post to Database Helper */
        async function sendDbAction(action, data = {}) {
            try {
                const fd = new FormData();
                fd.append('action', action);
                for (const k in data) {
                    fd.append(k, data[k]);
                }
                await fetch(window.location.href, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json' },
                    body: fd
                });
            } catch (e) {
                console.error('Error executing action:', e);
            }
        }

        /* Logika Aksi Penjual */
        async function terimaPengajuan(pengajuanId) {
            let list = getPenjualData();
            const item = list.find(b => b.id === activeModalBarangId);
            if (!item) return;

            const p = item.pengajuanList.find(x => x.id === pengajuanId);
            if (p) {
                p.status = 'Diterima';
                item.status = 'Proses';
                item.pembeliDiterima = p.pembeli;
                item.pembeliWa = p.wa;
                savePenjualData(list);
                closePengajuanModal();
                renderBarangList();

                // Save to Database
                await sendDbAction('terima_pengajuan', { transaction_id: pengajuanId, barang_id: item.id });
            }
        }

        async function tolakPengajuan(pengajuanId) {
            let list = getPenjualData();
            const item = list.find(b => b.id === activeModalBarangId);
            if (!item) return;

            const p = item.pengajuanList.find(x => x.id === pengajuanId);
            if (p) {
                p.status = 'Ditolak';
                savePenjualData(list);
                renderPengajuanModalTable(item);
                renderBarangList();

                // Save to Database
                await sendDbAction('tolak_pengajuan', { transaction_id: pengajuanId, barang_id: item.id });
            }
        }

        async function batalkanPengajuanInModal(pengajuanId) {
            let list = getPenjualData();
            const item = list.find(b => b.id === activeModalBarangId);
            if (!item) return;

            const p = item.pengajuanList.find(x => x.id === pengajuanId);
            if (p) {
                p.status = 'Ditolak';
                item.status = 'Tersedia';
                delete item.pembeliDiterima;
                delete item.pembeliWa;
                savePenjualData(list);
                closePengajuanModal();
                renderBarangList();

                // Save to Database
                await sendDbAction('batalkan_pengajuan_modal', { transaction_id: pengajuanId, barang_id: item.id });
            }
        }

        async function batalkanProses(barangId) {
            if (confirm('Kembalikan status barang menjadi Tersedia?')) {
                let list = getPenjualData();
                const item = list.find(b => b.id === barangId);
                if (item) {
                    item.status = 'Tersedia';
                    delete item.pembeliDiterima;
                    delete item.pembeliWa;
                    savePenjualData(list);
                    renderBarangList();

                    // Save to Database
                    await sendDbAction('batalkan_proses', { barang_id: barangId });
                }
            }
        }

        async function selesaikanTransaksi(barangId) {
            if (confirm('Tandai transaksi COD ini telah selesai? Barang akan diarsipkan sebagai Terjual.')) {
                let list = getPenjualData();
                const item = list.find(b => b.id === barangId);
                if (item) {
                    item.status = 'Terjual';
                    item.tglTerjual = new Date().toLocaleDateString('id-ID');
                    savePenjualData(list);
                    renderBarangList();

                    // Save to Database
                    await sendDbAction('selesaikan_transaksi', { barang_id: barangId });
                }
            }
        }

        async function hapusBarang(barangId) {
            if (confirm('Apakah Anda yakin ingin menghapus barang jualan ini?')) {
                let list = getPenjualData();
                list = list.filter(b => b.id !== barangId);
                savePenjualData(list);
                renderBarangList();

                // Save to Database
                await sendDbAction('hapus_barang', { barang_id: barangId });
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            try {
                localStorage.removeItem('pk_penjual_barang');
                localStorage.removeItem('pk_user_riwayat');
            } catch(e) {}
            renderBarangList();
        });
    </script>
</body>
</html>

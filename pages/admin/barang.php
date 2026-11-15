<?php
role_include('admin');
require_model('admin/products');
admin_handle_product_query('/admin/barang');
admin_handle_product_post('/admin/barang');

Component::include('admin/header', [
    'activeTab' => 'barang',
    'title' => 'Manajemen Barang',
    'subtitle' => 'Monitoring barang preloved mahasiswa, verifikasi kelayakan foto, dan take-down barang palsu atau melanggar.'
]);
?>
<section id="view-barang" class="admin-view-panel active">
    <div class="admin-table-card">
        <div class="admin-table-responsive">
            <table class="admin-table" id="admin-product-table" data-query-url="<?= esc(route('/admin/barang')) ?>">
                <thead>
                    <tr>
                        <th class="barang-table-index-heading">No</th>
                        <th>Nama Barang</th>
                        <th>Penjual</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th class="barang-table-actions-heading">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</section>

<div id="modalEditBarang" class="admin-modal-backdrop">
    <div class="admin-modal-card">
        <div class="admin-modal-header">
            <h2 class="admin-modal-title">EDIT STATUS BARANG</h2>
            <button type="button" class="admin-modal-close" onclick="closeModal('modalEditBarang')" aria-label="Tutup">&times;</button>
        </div>
        <form method="post">
            <input type="hidden" name="action" value="update-product">
            <input type="hidden" id="editBarangId" name="id">
            <div class="admin-modal-body">
                <div class="admin-info-box">
                    <div class="admin-info-box-title">Informasi Barang</div>
                    <div class="admin-info-row"><span class="admin-info-label">Nama Barang:</span><span id="editBarangNamaView" class="admin-info-value"></span></div>
                    <div class="admin-info-row"><span class="admin-info-label">Penjual:</span><span id="editBarangPenjualView" class="admin-info-value"></span></div>
                </div>
                <div class="admin-form-group">
                    <span class="admin-form-label">Tindakan Admin</span>
                    <div class="admin-radio-group">
                        <label class="admin-radio-label"><input type="radio" name="status" value="Tersedia" required><span><strong>Biarkan Tersedia</strong> - Barang tetap tayang di katalog publik</span></label>
                        <label class="admin-radio-label"><input type="radio" name="status" value="Dihapus" required><span><strong>Dihapus (Take Down)</strong> - Tarik barang dari katalog</span></label>
                    </div>
                </div>
                <div class="admin-form-group">
                    <label class="admin-form-label" for="editBarangAlasan">Alasan Hapus</label>
                    <textarea id="editBarangAlasan" name="moderation_note" class="admin-form-textarea" placeholder="Contoh: Barang terindikasi tidak sesuai dengan foto / palsu"></textarea>
                </div>
            </div>
            <div class="admin-modal-footer">
                <button type="button" class="btn-admin-secondary" onclick="closeModal('modalEditBarang')">Batal</button>
                <button type="submit" class="btn-admin-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<div id="modalDetailBarang" class="admin-modal-backdrop">
    <div class="admin-modal-card barang-detail-modal">
        <div class="admin-modal-header">
            <h2 class="admin-modal-title">DETAIL BARANG &amp; RIWAYAT PENGAJUAN</h2>
            <button type="button" class="admin-modal-close" onclick="closeModal('modalDetailBarang')" aria-label="Tutup">&times;</button>
        </div>
        <div class="admin-modal-body">
            <div class="admin-info-box">
                <div class="admin-info-box-title">Informasi Barang</div>
                <div class="admin-info-row"><span class="admin-info-label">Nama Barang:</span><span id="detailBarangNama" class="admin-info-value"></span></div>
                <div class="admin-info-row"><span class="admin-info-label">Penjual:</span><span id="detailBarangPenjual" class="admin-info-value"></span></div>
                <div class="admin-info-row"><span class="admin-info-label">Status Barang:</span><span id="detailBarangStatus" class="admin-info-value"></span></div>
            </div>
            <div class="barang-request-section">
                <div class="barang-request-title">Daftar Pengajuan Masuk (Read-Only)</div>
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
                        <tr>
                            <td colspan="4">Pilih detail barang untuk melihat pengajuan.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="admin-system-notice">Catatan Sistem: Hak akses Admin dibatasi. Admin hanya dapat memantau status transaksi tanpa bisa mengubah keputusan ACC/Tolak penjual.</div>
        </div>
        <div class="admin-modal-footer">
            <button type="button" class="btn-admin-primary" onclick="closeModal('modalDetailBarang')">Tutup Detail</button>
        </div>
    </div>
</div>

<?php Component::flush(); ?>
<script>
    const productTable = document.getElementById('admin-product-table');
    const queryUrl = productTable.dataset.queryUrl;

    function openEditBarang(product) {
        document.getElementById('editBarangId').value = product.id;
        document.getElementById('editBarangNamaView').textContent = product.nama || '-';
        document.getElementById('editBarangPenjualView').textContent = product.penjual || '-';
        document.querySelectorAll('#modalEditBarang input[name="status"]').forEach(input => input.checked = input.value === product.status);
        document.getElementById('editBarangAlasan').value = product.note || '';
        openModal('modalEditBarang');
    }

    function openDetailBarang(product) {
        document.getElementById('detailBarangNama').textContent = `${product.nama || '-'} (ID: ${product.id})`;
        document.getElementById('detailBarangPenjual').textContent = product.penjual || '-';
        document.getElementById('detailBarangStatus').textContent = product.status || '-';
        const body = document.getElementById('detailBarangPengajuanBody');
        body.replaceChildren();
        const requests = Array.isArray(product.requests) ? product.requests : [];
        if (!requests.length) {
            const row = body.insertRow();
            row.insertCell().colSpan = 4;
            row.cells[0].textContent = 'Belum ada pengajuan untuk barang ini.';
        } else {
            requests.forEach(request => {
                const row = body.insertRow();
                [request.pembeli, request.kampus, request.waktu, request.status].forEach(value => row.insertCell().textContent = value || '');
            });
        }
        openModal('modalDetailBarang');
    }

    async function loadProduct(button, callback) {
        button.disabled = true;
        try {
            const response = await fetch(queryUrl, {
                method: 'QUERY',
                headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
                body: JSON.stringify({ query: 'edit-product', id: button.dataset.id })
            });
            const product = await response.json();
            if (!response.ok) throw new Error(product.error || 'Gagal memuat barang.');
            callback(product);
        } catch (error) {
            Swal.fire('Gagal memuat barang', error.message, 'error');
        } finally {
            button.disabled = false;
        }
    }

    new DataTable(productTable, {
        processing: true,
        serverSide: true,
        ajax: (data, callback) => fetch(queryUrl, {
            method: 'QUERY',
            headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
            body: JSON.stringify({ ...data, query: 'table' })
        }).then(response => {
            if (!response.ok) throw new Error('Gagal memuat barang.');
            return response.json();
        }).then(callback).catch(() => Swal.fire('Gagal memuat barang', 'Muat ulang halaman dan coba lagi.', 'error')),
        columns: [
            {
                data: null,
                orderable: false,
                searchable: false,
                render: (_data, _type, _row, meta) => Number(meta.settings?._iDisplayStart ?? 0) + Number(meta.row ?? 0) + 1
            },
            { data: 'nama', render: value => esc(value || '-') },
            { data: 'penjual', render: value => esc(value || '-') },
            { data: 'kategori', render: value => esc(value || '-') },
            {
                data: 'status',
                render: value => `<span class="admin-badge ${value === 'Tersedia' ? 'admin-badge-success' : value === 'Dihapus' ? 'admin-badge-danger' : 'admin-badge-warning'}">${esc(value || '-')}</span>`
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: (_data, _type, row) => `<div class="admin-action-group"><button type="button" class="btn-action btn-action-edit product-edit-button" data-id="${Number(row.id)}"><i data-lucide="pencil" aria-hidden="true"></i>Edit</button><button type="button" class="btn-action btn-action-detail product-detail-button" data-id="${Number(row.id)}"><i data-lucide="eye" aria-hidden="true"></i>Detail</button></div>`
            }
        ],
        drawCallback: () => lucide.createIcons(),
        layout: { topStart: 'pageLength', topEnd: 'search', bottomStart: 'info', bottomEnd: { paging: { firstLast: false } } },
        language: { emptyTable: 'Belum ada barang di katalog.', processing: 'Memuat barang…' }
    });

    productTable.addEventListener('click', event => {
        const editButton = event.target.closest('.product-edit-button[data-id]');
        if (editButton) {
            loadProduct(editButton, openEditBarang);
            return;
        }
        const detailButton = event.target.closest('.product-detail-button[data-id]');
        if (detailButton) loadProduct(detailButton, openDetailBarang);
    });
</script>
<?php $scripts = Component::endFlush(); ?>
<?php Component::include('admin/footer', ['scripts' => $scripts]); ?>

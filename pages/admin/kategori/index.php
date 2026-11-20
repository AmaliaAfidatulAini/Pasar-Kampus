<?php
role_include('admin');
?>

<?php Component::flush(); ?>
    <button type="button" class="btn-admin-primary admin-user-add" onclick="openAddKategoriModal()">
        <i data-lucide="plus" aria-hidden="true"></i>Tambah Data
    </button>
<?php $actionBtn = Component::endFlush(); ?>

<?php
Component::include('admin/header', [
    'activeTab' => 'kategori',
    'title' => 'Manajemen Kategori',
    'subtitle' => 'Kelola kategori yang digunakan untuk mengatur katalog barang.',
    'actionBtn' => $actionBtn
]);
?>

<section id="view-kategori" class="admin-view-panel active">
    <div class="admin-table-card">
        <div class="admin-table-responsive">
            <table class="admin-table" id="admin-category-table" data-query-url="<?= esc(route('/admin/kategori/get')) ?>">
                <thead>
                    <tr>
                        <th style="width: 70px;">No.</th>
                        <th>Nama</th>
                        <th>Jumlah</th>
                        <th style="width: 170px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</section>

<div id="modalKategori" class="admin-modal-backdrop">
    <div class="admin-modal-card">
        <div class="admin-modal-header">
            <h2 id="kategoriModalTitle" class="admin-modal-title">TAMBAH KATEGORI</h2>
            <button type="button" class="admin-modal-close" onclick="closeModal('modalKategori')" aria-label="Tutup">&times;</button>
        </div>
        <form id="kategoriForm" method="post" action="<?= esc(route('/admin/kategori/post')) ?>">
            <input type="hidden" name="target" value="create">
            <input type="hidden" name="id" value="">
            <div class="admin-modal-body">
                <div class="admin-form-group">
                    <label class="admin-form-label" for="kategoriNama">Nama Kategori <span class="req">*</span></label>
                    <input type="text" name="nama" id="kategoriNama" required class="admin-form-input" placeholder="Buku">
                </div>
            </div>
            <div class="admin-modal-footer">
                <button type="button" class="btn-admin-secondary" onclick="closeModal('modalKategori')">Batal</button>
                <button type="submit" class="btn-admin-primary" id="kategoriSaveButton">Simpan</button>
            </div>
        </form>
    </div>
</div>

<?php Component::flush(); ?>
<script>
    function openAddKategoriModal() {
        const form = document.getElementById('kategoriForm');
        if (!form) return;
        form.reset();
        form.elements.id.value = '';
        form.elements.target.value = 'create';
        document.getElementById('kategoriModalTitle').textContent = 'TAMBAH KATEGORI';
        document.getElementById('kategoriSaveButton').textContent = 'Tambah';
        openModal('modalKategori');
    }

    function openEditKategoriModal(category) {
        const form = document.getElementById('kategoriForm');
        if (!form) return;
        form.elements.target.value = 'update';
        form.elements.id.value = category.id;
        form.elements.nama.value = category.nama;
        document.getElementById('kategoriModalTitle').textContent = 'EDIT KATEGORI';
        document.getElementById('kategoriSaveButton').textContent = 'Simpan';
        openModal('modalKategori');
    }

    const categoryTable = document.getElementById('admin-category-table');
    const queryUrl = categoryTable.dataset.queryUrl;
    new DataTable(categoryTable, {
        processing: true,
        serverSide: true,
        ajax: queryUrl + '?query=table',
        columns: [
            { data: null, orderable: false, searchable: false, render: (_data, _type, _row, meta) => Number(meta.settings?._iDisplayStart ?? 0) + Number(meta.row ?? 0) + 1 },
            { data: 'nama', render: value => esc(value) },
            { data: 'jmlBarang', render: value => esc(value) },
            { data: null, orderable: false, searchable: false, render: (_data, _type, row) => `<div class="admin-action-group"><button type="button" class="btn-action btn-action-edit" data-id="${Number(row.id)}"><i data-lucide="pencil" aria-hidden="true"></i>Edit</button><form method="post" action="<?= esc(route('/admin/kategori/post')) ?>" class="category-delete-form" style="display:inline"><input type="hidden" name="target" value="delete"><input type="hidden" name="id" value="${Number(row.id)}"><button type="submit" class="btn-action btn-action-danger"><i data-lucide="trash-2" aria-hidden="true"></i>Hapus</button></form></div>` }
        ],
        drawCallback: () => {
            lucide.createIcons();
            document.querySelectorAll('.category-delete-form').forEach(form => {
                if (form.dataset.confirmBound) return;
                form.dataset.confirmBound = 'true';
                form.addEventListener('submit', async event => {
                    event.preventDefault();
                    const result = await Swal.fire({ title: 'Hapus kategori?', text: 'Kategori hanya dapat dihapus jika belum dipakai barang.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Hapus', cancelButtonText: 'Batal' });
                    if (result.isConfirmed) form.submit();
                });
            });
        },
        layout: { topStart: 'pageLength', topEnd: 'search', bottomStart: 'info', bottomEnd: { paging: { firstLast: false } } },
        language: { emptyTable: 'Belum ada kategori.', processing: 'Memuat kategori…' }
    });

    categoryTable.addEventListener('click', async event => {
        const button = event.target.closest('.btn-action-edit[data-id]');
        if (!button) return;
        button.disabled = true;
        try {
            const response = await fetch(`${queryUrl}?${new URLSearchParams({ query: 'single', id: button.dataset.id })}`, {
                headers: { Accept: 'application/json' }
            });
            const category = await response.json();
            if (!response.ok) throw new Error(category.error || 'Gagal memuat kategori.');
            openEditKategoriModal(category);
        } catch (error) {
            Swal.fire('Gagal memuat kategori', error.message, 'error');
        } finally {
            button.disabled = false;
        }
    });
</script>
<?php $scripts = Component::endFlush(); ?>
<?php Component::include('admin/footer', ['scripts' => $scripts]); ?>
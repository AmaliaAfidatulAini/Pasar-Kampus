<?php
role_include('admin');

require_model('admin/tags');
admin_handle_tag_query('/admin/tags');
admin_handle_tag_post('/admin/tags');
?>

<?php Component::flush(); ?>
    <button type="button" class="btn-admin-primary admin-user-add" onclick="openAddTagModal()">
        <i data-lucide="plus" aria-hidden="true"></i>Tambah Data
    </button>
<?php $actionBtn = Component::endFlush(); ?>

<?php
Component::include('admin/header', [
    'activeTab' => 'tags',
    'title' => 'Manajemen Tag',
    'subtitle' => 'Kelola tag yang digunakan untuk mengelompokkan barang di katalog.',
    'actionBtn' => $actionBtn
]);
?>

<section id="view-tags" class="admin-view-panel active">
    <div class="admin-table-card">
        <div class="admin-table-responsive">
            <table class="admin-table" id="admin-tag-table" data-query-url="<?= esc(route('/admin/tags')) ?>">
                <thead>
                    <tr>
                        <th style="width: 70px; text-align: center;">No</th>
                        <th style="text-align: start;">Nama</th>
                        <th style="text-align: center;">Jumlah</th>
                        <th style="width: 170px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</section>

<div id="modalTag" class="admin-modal-backdrop">
    <div class="admin-modal-card">
        <div class="admin-modal-header">
            <h2 id="tagModalTitle" class="admin-modal-title">TAMBAH TAG</h2>
            <button type="button" class="admin-modal-close" onclick="closeModal('modalTag')" aria-label="Tutup">&times;</button>
        </div>
        <form id="tagForm" method="post">
            <input type="hidden" name="action" value="save-tag">
            <input type="hidden" name="id" value="">
            <div class="admin-modal-body">
                <div class="admin-form-group">
                    <label class="admin-form-label" for="tagNama">Nama Tag <span class="req">*</span></label>
                    <input type="text" name="nama" id="tagNama" required class="admin-form-input" placeholder="#ATK">
                </div>
            </div>
            <div class="admin-modal-footer">
                <button type="button" class="btn-admin-secondary" onclick="closeModal('modalTag')">Batal</button>
                <button type="submit" class="btn-admin-primary" id="tagSaveButton">Simpan</button>
            </div>
        </form>
    </div>
</div>

<?php Component::flush(); ?>
<script>
    function openAddTagModal() {
        const form = document.getElementById('tagForm');
        if (!form) return;
        form.reset();
        form.elements.id.value = '';
        form.elements.action.value = 'save-tag';
        document.getElementById('tagModalTitle').textContent = 'TAMBAH TAG';
        document.getElementById('tagSaveButton').textContent = 'Tambah';
        openModal('modalTag');
    }

    function openEditTagModal(tag) {
        const form = document.getElementById('tagForm');
        if (!form) return;
        form.elements.action.value = 'save-tag';
        form.elements.id.value = tag.id;
        form.elements.nama.value = tag.nama;
        document.getElementById('tagModalTitle').textContent = 'EDIT TAG';
        document.getElementById('tagSaveButton').textContent = 'Simpan';
        openModal('modalTag');
    }

    const tagTable = document.getElementById('admin-tag-table');
    const queryUrl = tagTable.dataset.queryUrl;
    new DataTable(tagTable, {
        processing: true,
        serverSide: true,
        ajax: (data, callback) => fetch(queryUrl, {
            method: 'QUERY',
            headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
            body: JSON.stringify({ ...data, query: 'table' })
        }).then(response => {
            if (!response.ok) throw new Error('Gagal memuat tag.');
            return response.json();
        }).then(callback).catch(() => Swal.fire('Gagal memuat tag', 'Muat ulang halaman dan coba lagi.', 'error')),
        columns: [
            { data: null, orderable: false, searchable: false, render: (_data, _type, _row, meta) => Number(meta.settings?._iDisplayStart ?? 0) + Number(meta.row ?? 0) + 1 },
            { data: 'nama', render: value => esc(value) },
            { data: 'jmlProduk', render: value => esc(value) },
            { data: null, orderable: false, searchable: false, render: (_data, _type, row) => `<div class="admin-action-group"><button type="button" class="btn-action btn-action-edit" data-id="${Number(row.id)}"><i data-lucide="pencil" aria-hidden="true"></i>Edit</button><form method="post" class="tag-delete-form" style="display:inline"><input type="hidden" name="action" value="delete-tag"><input type="hidden" name="id" value="${Number(row.id)}"><button type="submit" class="btn-action btn-action-danger"><i data-lucide="trash-2" aria-hidden="true"></i>Hapus</button></form></div>` }
        ],
        drawCallback: () => {
            lucide.createIcons();
            document.querySelectorAll('.tag-delete-form').forEach(form => {
                if (form.dataset.confirmBound) return;
                form.dataset.confirmBound = 'true';
                form.addEventListener('submit', async event => {
                    event.preventDefault();
                    const result = await Swal.fire({ title: 'Hapus tag?', text: 'Tag yang dihapus tidak dapat dipulihkan.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Hapus', cancelButtonText: 'Batal' });
                    if (result.isConfirmed) form.submit();
                });
            });
        },
        layout: { topStart: 'pageLength', topEnd: 'search', bottomStart: 'info', bottomEnd: { paging: { firstLast: false } } },
        language: { emptyTable: 'Belum ada tag.', processing: 'Memuat tag…' }
    });

    tagTable.addEventListener('click', async event => {
        const button = event.target.closest('.btn-action-edit[data-id]');
        if (!button) return;
        button.disabled = true;
        try {
            const response = await fetch(queryUrl, {
                method: 'QUERY',
                headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
                body: JSON.stringify({ query: 'edit-tag', id: button.dataset.id })
            });
            const tag = await response.json();
            if (!response.ok) throw new Error(tag.error || 'Gagal memuat tag.');
            openEditTagModal(tag);
        } catch (error) {
            Swal.fire('Gagal memuat tag', error.message, 'error');
        } finally {
            button.disabled = false;
        }
    });
</script>
<?php $scripts = Component::endFlush(); ?>

<?php Component::include('admin/footer', ['scripts' => $scripts]); ?>

<?php
role_include('admin');
require_model('admin/users');
admin_handle_user_query('/admin/users');
admin_handle_user_post('/admin/users');
$session = get_session();
?>
<?php Component::flush(); ?>
    <button type="button" class="btn-admin-primary admin-user-add" onclick="openAddUserModal()">
        <i data-lucide="plus" aria-hidden="true"></i>Tambah Data
    </button>
<?php $actionBtn = Component::endFlush(); ?>

<?php
    Component::include('admin/header', [
        'activeTab' => 'users',
        'title' => 'Manajemen Pengguna',
        'subtitle' => 'Kelola data pengguna terdaftar, hak akses, serta penegakan sanksi pengguna.',
        'actionBtn' => $actionBtn
    ]);
?>

<section id="view-users" class="admin-view-panel active">
    <div class="admin-table-card">
        <div class="admin-table-responsive">
            <table class="admin-table" id="admin-table" data-id="<?= (int) ($session['user_id'] ?? 0) ?>" data-query-url="<?= esc(route('/admin/users')) ?>">
                <thead>
                    <tr>
                        <th style="width: 70px;">No.</th>
                        <th>Nama</th>
                        <th>NIM</th>
                        <th>Kampus</th>
                        <th>Status</th>
                        <th style="width: 190px;">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</section>

<div id="userModal" class="admin-modal-backdrop">
    <div class="admin-modal-card">
        <div class="admin-modal-header">
            <h2 class="admin-modal-title" id="userModalTitle">TAMBAH PENGGUNA</h2><button type="button" class="admin-modal-close" onclick="closeModal('userModal')" aria-label="Tutup">&times;</button>
        </div>
        <form method="post" id="userForm">
            <input type="hidden" name="action" value="create-user">
            <input type="hidden" name="id" value="">
            <div class="admin-modal-body">
                <div class="admin-form-group">
                    <label class="admin-form-label" for="userEmail">Email <span class="req">*</span></label>
                    <input type="email" name="email" id="userEmail" required class="admin-form-input" placeholder="mahasiswa@student.ub.ac.id">
                </div>
                <div class="admin-form-group" id="userPasswordGroup">
                    <label class="admin-form-label" for="userPassword">Kata Sandi <span class="req" id="userPasswordRequired">*</span></label>
                    <input type="password" name="password" id="userPassword" required class="admin-form-input" placeholder="Kata sandi akun">
                </div>
                <div class="admin-form-group" id="userRoleGroup">
                    <span class="admin-form-label">Role Akun</span>
                    <div class="admin-radio-group" style="flex-direction: row; gap: 20px;">
                        <label class="admin-radio-label" style="flex: 1;"><input type="radio" name="role" value="Mahasiswa" checked><span>Pengguna</span></label>
                        <label class="admin-radio-label" style="flex: 1;"><input type="radio" name="role" value="Admin"><span>Admin</span></label>
                    </div>
                </div>
                <hr style="opacity: 0.5; margin-bottom: 1rem;">
                <div class="admin-form-group">
                    <label class="admin-form-label" for="userName">Nama Lengkap <span class="req">*</span></label>
                    <input type="text" name="name" id="userName" required class="admin-form-input" placeholder="Masukkan nama lengkap">
                </div>
                <div class="admin-form-group">
                    <label class="admin-form-label" for="userNim">NIM <span class="req">*</span></label>
                    <input type="text" name="nim" id="userNim" required class="admin-form-input" placeholder="2141720XXXX">
                </div>
                <div class="admin-form-group">
                    <label class="admin-form-label" for="userCampus">Asal Kampus</label>
                    <select name="campus" id="userCampus" required class="admin-form-select">
                        <option value="Universitas Brawijaya">Universitas Brawijaya (UB)</option>
                        <option value="Universitas Negeri Malang">Universitas Negeri Malang (UM)</option>
                        <option value="Univ. Muhammadiyah Malang">Univ. Muhammadiyah Malang (UMM)</option>
                        <option value="Politeknik Negeri Malang">Politeknik Negeri Malang (POLINEMA)</option>
                    </select>
                </div>
                <div id="userSanctionFields" hidden>
                    <hr style="opacity: 0.5; margin-bottom: 1rem;">
                    <div class="admin-form-group"><span class="admin-form-label">Ubah Status</span>
                        <div class="admin-radio-group">
                            <label class="admin-radio-label"><input type="radio" name="status" value="Aktif">
                                <span><strong>Aktif</strong> - Akun dapat bertransaksi, pasang barang, dan COD normal</span>
                            </label>
                            <label class="admin-radio-label"><input type="radio" name="status" value="Suspend">
                                <span><strong>Suspend</strong> - Akun dibekukan sementara karena peringatan/pelanggaran</span>
                            </label>
                            <label class="admin-radio-label"><input type="radio" name="status" value="Banned">
                                <span><strong>Banned</strong> - Blokir permanen akun terkait tindakan penipuan</span>
                            </label>
                        </div>
                    </div>
                    <div class="admin-form-group"><label class="admin-form-label" for="userReason">Catatan</label><textarea name="reason" id="userReason" class="admin-form-textarea" placeholder="Tuliskan catatan alasan sanksi..."></textarea>
                        <div class="admin-form-note">Catatan internal untuk tindakan moderasi.</div>
                    </div>
                </div>
            </div>
            <div class="admin-modal-footer">
                <button type="button" class="btn-admin-secondary" onclick="closeModal('userModal')">
                    Batal
                </button>
                <button type="submit" class="btn-admin-primary" id="userSaveButton">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<?php Component::flush() ?>
<script>
    const userForm = document.getElementById('userForm');

    const setUserModalMode = (editing, user = {}) => {
        const field = name => userForm.elements.namedItem(name);
        field('action').value = editing ? 'update-user' : 'create-user';
        field('id').value = editing ? user.id : '';
        field('name').value = editing ? user.name : '';
        field('nim').value = editing ? user.nim : '';
        field('email').value = editing ? user.email : '';
        field('campus').value = editing ? user.campus : 'Universitas Brawijaya';
        field('password').value = '';
        field('password').required = !editing;
        document.getElementById('userPasswordRequired').textContent = editing ? '(opsional)' : '*';
        field('nim').required = !editing;
        field('campus').required = !editing;
        document.getElementById('userPasswordGroup').hidden = false;
        field('password').disabled = false;
        document.getElementById('userRoleGroup').hidden = editing;
        userForm.querySelectorAll('[name="role"]').forEach(radio => radio.disabled = editing);
        document.getElementById('userModalTitle').textContent = editing ? 'EDIT PENGGUNA' : 'TAMBAH PENGGUNA';
        document.getElementById('userSaveButton').textContent = editing ? 'Simpan' : 'Tambah';
        const showSanctions = editing && user.role === 'Mahasiswa';
        document.getElementById('userSanctionFields').hidden = !showSanctions;
        userForm.querySelectorAll('[name="status"]').forEach(radio => {
            radio.checked = showSanctions && radio.value === user.status;
            radio.disabled = !showSanctions;
        });
        field('reason').value = showSanctions ? user.reason : '';
        field('reason').disabled = !showSanctions;
    };
    function openAddUserModal() {
        userForm.reset();
        setUserModalMode(false);
        openModal('userModal');
    }
    window.openEditUserModal = user => {
        setUserModalMode(true, user);
        openModal('userModal');
    };
    new DataTable(document.getElementById('admin-table'), {
        processing: true,
        serverSide: true,
        ajax: (data, callback) => fetch(document.getElementById('admin-table').dataset.queryUrl, {
            method: 'QUERY',
            headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
            body: JSON.stringify({ ...data, query: 'table' })
        }).then(response => {
            if (!response.ok) throw new Error('Gagal memuat pengguna.');
            return response.json();
        }).then(callback).catch(() => Swal.fire('Gagal memuat pengguna', 'Muat ulang halaman dan coba lagi.', 'error')),
        columns: [
            { data: null, orderable: false, searchable: false, render: (_data, _type, _row, meta) => Number(meta.settings?._iDisplayStart ?? 0) + Number(meta.row ?? 0) + 1 },
            { data: null, render: (_data, _type, row) => row.role === 'Admin'
                ? `<strong>${esc(row.email)}</strong> <span class="admin-badge admin-role-badge">ADMIN</span>`
                : `<strong>${esc(row.nama || row.email)}</strong>${row.nama ? `<div>${esc(row.email)}</div>` : ''}` },
            { data: 'nim', render: value => esc(value || '-') },
            { data: 'kampus', render: value => esc(value || '-') },
            { data: 'status', render: value => `<span class="admin-badge ${value === 'Aktif' ? 'admin-badge-success' : value === 'Suspend' ? 'admin-badge-warning' : 'admin-badge-danger'}">${esc(value)}</span>` },
            { data: null, orderable: false, searchable: false, render: (_data, _type, row) => `<div class="admin-action-group"><button type="button" class="btn-action btn-action-edit" data-id="${Number(row.id)}"><i data-lucide="pencil" aria-hidden="true"></i>Edit</button><form method="post" class="user-delete-form" style="display:inline"><input type="hidden" name="action" value="delete-user"><input type="hidden" name="id" value="${Number(row.id)}"><button type="submit" class="btn-action btn-action-danger"><i data-lucide="trash-2" aria-hidden="true"></i>Hapus</button></form></div>` }
        ],
        rowCallback: (row, data) => row.classList.toggle('current-user-row', Number(data.id) === Number(document.getElementById('admin-table').dataset.id)),
        drawCallback: () => {
            lucide.createIcons();
            document.querySelectorAll('.user-delete-form').forEach(form => {
                if (form.dataset.confirmBound) return;
                form.dataset.confirmBound = 'true';
                form.addEventListener('submit', async event => {
                    event.preventDefault();
                    const result = await Swal.fire({ title: 'Hapus pengguna?', text: 'Pengguna ini akan dihapus.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Hapus', cancelButtonText: 'Batal' });
                    if (result.isConfirmed) form.submit();
                });
            });
        },
        layout: { topEnd: 'search', bottomEnd: { paging: { firstLast: false } } },
        language: { emptyTable: 'Belum ada pengguna.', processing: 'Memuat pengguna…' }
    });

    document.getElementById('admin-table').addEventListener('click', async event => {
        const button = event.target.closest('.btn-action-edit[data-id]');
        if (!button) return;
        button.disabled = true;
        try {
            const response = await fetch(document.getElementById('admin-table').dataset.queryUrl, {
                method: 'QUERY',
                headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
                body: JSON.stringify({ query: 'edit-user', id: button.dataset.id })
            });
            const user = await response.json();
            if (!response.ok) throw new Error(user.error || 'Gagal memuat pengguna.');
            window.openEditUserModal(user);
        } catch (error) {
            Swal.fire('Gagal memuat pengguna', error.message, 'error');
        } finally {
            button.disabled = false;
        }
    });
</script>
<?php $scripts = Component::endFlush(); ?>

<?php Component::include('admin/footer', ['scripts' => $scripts]); ?>
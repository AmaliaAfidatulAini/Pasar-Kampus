<?php
role_include('admin');
$userPreferenceCategories = executeQuery(
    'SELECT categories_id AS id, category_name AS name FROM categories ORDER BY category_name ASC',
    [],
    'fetchAll'
);
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
            <table class="admin-table" id="admin-table" data-id="<?= (int) ($session['user_id'] ?? 0) ?>" data-query-url="<?= esc(route('/admin/users/get')) ?>">
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
    <div class="admin-modal-card user-modal-card">
        <div class="admin-modal-header">
            <h2 class="admin-modal-title" id="userModalTitle">TAMBAH PENGGUNA</h2><button type="button" class="admin-modal-close" onclick="closeModal('userModal')" aria-label="Tutup">&times;</button>
        </div>
        <form method="post" id="userForm" action="<?= esc(route('/admin/users/post')) ?>">
            <input type="hidden" name="target" value="create">
            <input type="hidden" name="id" value="">
            <div class="admin-modal-body">
                <div class="admin-form-group">
                    <label class="admin-form-label" for="userEmail">Email <span class="req">*</span></label>
                    <input type="email" name="email" id="userEmail" required class="admin-form-input" placeholder="mahasiswa@student.ub.ac.id">
                </div>
                <div class="admin-form-group">
                    <label class="admin-form-label" for="userPassword">Kata Sandi <span class="req" id="userPasswordRequired">*</span></label>
                    <input type="password" name="password" id="userPassword" required class="admin-form-input" placeholder="Kata sandi akun">
                </div>
                <div class="admin-form-group" id="userRoleGroup">
                    <span class="admin-form-label" id="userRoleLabel">Role Akun <span class="req">*</span></span>
                    <div class="admin-radio-group user-role-options" role="group" aria-labelledby="userRoleLabel">
                        <label class="admin-radio-label"><input type="radio" name="role" value="Mahasiswa" required checked><span>Pengguna</span></label>
                        <label class="admin-radio-label"><input type="radio" name="role" value="Admin" required><span>Admin</span></label>
                    </div>
                </div>
                <hr class="user-profile-divider">
                <div class="user-profile-grid">
                    <div class="admin-form-group">
                        <label class="admin-form-label" for="userName">Nama Lengkap</label>
                        <input type="text" name="name" id="userName" maxlength="255" class="admin-form-input" placeholder="Nama lengkap">
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label" for="userPhone">Nomor Telepon</label>
                        <input type="tel" name="phone" id="userPhone" maxlength="50" class="admin-form-input" placeholder="Nomor telepon">
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label" for="userCampus">Asal Kampus</label>
                        <input type="text" name="campus" id="userCampus" maxlength="255" class="admin-form-input" placeholder="Nama kampus">
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label" for="userNim">NIM</label>
                        <input type="text" name="nim" id="userNim" maxlength="50" class="admin-form-input" placeholder="Nomor induk mahasiswa">
                    </div>
                    <div class="admin-form-group user-preferences-group">
                        <span class="admin-form-label" id="userPreferencesLabel">Preferensi Kategori</span>
                        <div class="user-preferences-grid" role="group" aria-labelledby="userPreferencesLabel">
                            <?php foreach ($userPreferenceCategories as $category): ?>
                                <label class="user-preference-option">
                                    <input type="checkbox" name="preference_categories[]" value="<?= (int) $category['id'] ?>">
                                    <span><?= esc($category['name']) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <?php if ($userPreferenceCategories === []): ?>
                            <div class="admin-form-note">Belum ada kategori yang tersedia.</div>
                        <?php endif; ?>
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label" for="userDistrict">Desa/Kelurahan</label>
                        <input type="text" name="district" id="userDistrict" maxlength="200" class="admin-form-input" placeholder="Desa atau kelurahan">
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label" for="userSubdistrict">Kecamatan</label>
                        <input type="text" name="subdistrict" id="userSubdistrict" maxlength="255" class="admin-form-input" placeholder="Kecamatan">
                    </div>
                </div>
                <div id="userSanctionFields" hidden>
                    <hr class="user-profile-divider">
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
    const queryUrl = document.getElementById('admin-table').dataset.queryUrl;

    const setUserModalMode = (editing, user = {}) => {
        const field = name => userForm.elements.namedItem(name);
        field('target').value = editing ? 'update' : 'create';
        field('id').value = editing ? user.id : '';
        field('name').value = editing ? user.name : '';
        field('phone').value = editing ? user.phone : '';
        field('nim').value = editing ? user.nim : '';
        field('campus').value = editing ? user.campus : '';
        const selectedPreferences = new Set(editing && Array.isArray(user.preferences) ? user.preferences.map(String) : []);
        userForm.querySelectorAll('[name="preference_categories[]"]').forEach(checkbox => {
            checkbox.checked = selectedPreferences.has(checkbox.value);
        });
        field('subdistrict').value = editing ? user.subdistrict : '';
        field('district').value = editing ? user.district : '';
        field('email').value = editing ? user.email : '';
        field('password').required = !editing;
        document.getElementById('userPasswordRequired').hidden = editing;
        field('role').forEach(radio => {
            radio.checked = radio.value === (editing ? user.role : 'Mahasiswa');
            radio.disabled = false;
        });
        document.getElementById('userRoleGroup').hidden = false;
        document.getElementById('userModalTitle').textContent = editing ? 'EDIT PENGGUNA' : 'TAMBAH PENGGUNA';
        document.getElementById('userSaveButton').textContent = editing ? 'Simpan' : 'Tambah';
        const syncSanctions = () => {
            const isMahasiswa = field('role').value === 'Mahasiswa';
            const showSanctions = editing && isMahasiswa;
            document.querySelector('.user-profile-divider').hidden = !isMahasiswa;
            document.querySelector('.user-profile-grid').hidden = !isMahasiswa;
            document.getElementById('userSanctionFields').hidden = !showSanctions;
            userForm.querySelectorAll('[name="status"]').forEach(radio => {
                radio.checked = showSanctions && radio.value === user.status;
                radio.disabled = !showSanctions;
                radio.required = showSanctions;
            });
            field('reason').value = showSanctions ? user.reason : '';
            field('reason').disabled = !showSanctions;
        };
        syncSanctions();
        userForm.querySelectorAll('[name="role"]').forEach(radio => radio.onchange = syncSanctions);
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
        ajax: queryUrl + '?query=table',
        columns: [
            { data: null, orderable: false, searchable: false, render: (_data, _type, _row, meta) => Number(meta.settings?._iDisplayStart ?? 0) + Number(meta.row ?? 0) + 1 },
            { data: null, render: (_data, _type, row) => row.role === 'Admin'
                ? `<strong>${esc(row.email)}</strong> <span class="admin-badge admin-role-badge">ADMIN</span>`
                : `<strong>${esc(row.nama || row.email)}</strong>${row.nama ? `<div>${esc(row.email)}</div>` : ''}` },
            { data: 'nim', render: value => esc(value || '-') },
            { data: 'kampus', render: value => esc(value || '-') },
            { data: 'status', render: value => `<span class="admin-badge ${value === 'Aktif' ? 'admin-badge-success' : value === 'Suspend' ? 'admin-badge-warning' : 'admin-badge-danger'}">${esc(value)}</span>` },
            { data: null, orderable: false, searchable: false, render: (_data, _type, row) => {
                const isSelf = Number(row.id) === Number(document.getElementById('admin-table').dataset.id);
                return `<div class="admin-action-group"><button type="button" class="btn-action btn-action-edit" data-id="${Number(row.id)}"><i data-lucide="pencil" aria-hidden="true"></i>Edit</button><form method="post" action="<?= esc(route('/admin/users/post')) ?>" class="user-delete-form" style="display:inline"><input type="hidden" name="target" value="delete"><input type="hidden" name="id" value="${Number(row.id)}"><button type="submit" class="btn-action btn-action-danger" ${isSelf ? 'disabled title="Tidak dapat menghapus akun sendiri"' : ''}><i data-lucide="trash-2" aria-hidden="true"></i>Hapus</button></form></div>`;
            } }
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
            const params = new URLSearchParams({ query: 'single', id: button.dataset.id });
            const response = await fetch(`${queryUrl}?${params}`, {
                headers: { Accept: 'application/json' }
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
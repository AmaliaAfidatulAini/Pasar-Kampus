<?php
$session = session_include('/login');
$userId = (int) ($session['user_id'] ?? 0);

$profile = fetch(executeQuery(
    "SELECT u.email,
            p.name,
            p.phone,
            p.campus,
            p.student_number,
            p.subdistrict,
            p.district,
            avatar.file_path AS avatar_url,
            COALESCE((
                SELECT json_agg(up.category_id ORDER BY up.category_id)
                FROM user_preferences up
                WHERE up.profile_id = p.profile_id
            ), '[]'::json) AS category_ids
     FROM users u
     LEFT JOIN LATERAL (
         SELECT *
         FROM user_profiles
         WHERE user_id = u.user_id
         ORDER BY profile_id DESC
         LIMIT 1
     ) p ON TRUE
     LEFT JOIN attachments avatar ON avatar.attachment_id = p.attachment_id
     WHERE u.user_id = ?",
    [$userId]
)) ?: [];

$categories = executeQuery(
    'SELECT categories_id AS id, category_name AS name FROM categories ORDER BY category_name',
    [],
    'fetchAll'
);
$selectedCategories = array_map('strval', json_decode((string) ($profile['category_ids'] ?? '[]'), true) ?: []);
$profileName = trim((string) ($profile['name'] ?? '')) ?: 'Pengguna';
$avatarUrl = trim((string) ($profile['avatar_url'] ?? ''));
$hasAvatar = $avatarUrl !== '';
$avatarUrl = $hasAvatar ? $avatarUrl : asset('img/profile-placeholder.webp');
?>

<?php Component::include('user/header', ['title' => 'Profil Saya']); ?>

<main class="container py-4 py-lg-5">
    <?php Component::include("common/banner", [
        "title" => 'Profil Saya',
        "subtitle" => "Ubah biodata, preferensi, dan password akun",
    ]) ?>

    <form class="card mb-4" id="profile-form" method="post" action="<?= esc(route('/user/post')) ?>">
        <div class="card-header p-4">
            <h2 class="h4 fw-bold mb-0 text-primary">Informasi Akun</h2>
        </div>

        <div class="card-body p-4">
            <div class="d-flex flex-column flex-sm-row align-items-center gap-4">
                <div>
                    <button type="button" class="pk-avatar-trigger" data-bs-toggle="modal" data-bs-target="#avatarModal" aria-label="Ubah foto profil">
                        <span class="pk-avatar pk-avatar--lg">
                            <img id="profile-avatar-preview" src="<?= esc($avatarUrl) ?>" alt="Foto profil <?= esc($profileName) ?>">
                        </span>
                        <span class="pk-avatar-edit">
                            <i data-lucide="pencil" class="pk-icon" aria-hidden="true"></i>
                        </span>
                    </button>
                </div>
                <div class="text-center text-sm-start">
                    <h2 class="h4 fw-bold mb-1" id="profile-heading"><?= esc($profileName) ?></h2>
                    <p class="text-body-secondary mb-0"><?= esc($profile['email']) ?></p>
                </div>
            </div>

            <input type="hidden" name="target" value="profile">
            <hr>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold small" for="profile-name">Nama Lengkap</label>
                    <input class="form-control" id="profile-name" name="name" type="text" maxlength="255" value="<?= esc((string) ($profile['name'] ?? '')) ?>" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold small" for="profile-email">Email</label>
                    <input class="form-control bg-body-tertiary" id="profile-email" type="email" value="<?= esc((string) ($profile['email'] ?? '')) ?>" readonly>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold small" for="profile-phone">Nomor Telepon</label>
                    <div class="input-group">
                        <span class="input-group-text"><i data-lucide="phone" style="width: 16px;" aria-hidden="true"></i></span>
                        <input class="form-control" id="profile-phone" name="phone" type="tel" maxlength="50" value="<?= esc((string) ($profile['phone'] ?? '')) ?>" placeholder="Nomor telepon">
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold small" for="profile-campus">Asal Kampus</label>
                    <input class="form-control" id="profile-campus" name="campus" type="text" maxlength="255" value="<?= esc((string) ($profile['campus'] ?? '')) ?>" placeholder="Nama kampus">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold small" for="profile-student-number">NIM</label>
                    <input class="form-control" id="profile-student-number" name="student_number" type="text" maxlength="50" value="<?= esc((string) ($profile['student_number'] ?? '')) ?>" placeholder="Nomor induk mahasiswa">
                </div>

                <fieldset class="col-12">
                    <legend class="form-label fw-semibold small">Preferensi Kategori</legend>
                    <div class="row row-cols-1 row-cols-sm-2 g-2 row-gap-2">
                        <?php foreach ($categories as $category): ?>
                            <div class="col">
                                <label class="d-flex align-items-center gap-2 h-100">
                                    <input class="form-check-input m-0" type="checkbox" name="preferences[]" value="<?= (int) $category['id'] ?>" <?= in_array((string) $category['id'], $selectedCategories, true) ? 'checked' : '' ?>>
                                    <span class="small"><?= esc($category['name']) ?></span>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </fieldset>

                <div class="col-md-6">
                    <label class="form-label fw-semibold small" for="profile-subdistrict">Desa/Kelurahan</label>
                    <input class="form-control" id="profile-subdistrict" name="subdistrict" type="text" maxlength="255" value="<?= esc((string) ($profile['subdistrict'] ?? '')) ?>" placeholder="Desa atau kelurahan">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold small" for="profile-district">Kecamatan</label>
                    <input class="form-control" id="profile-district" name="district" type="text" maxlength="200" value="<?= esc((string) ($profile['district'] ?? '')) ?>" placeholder="Kecamatan">
                </div>
            </div>
        </div>

        <div class="card-footer p-4">
            <button class="btn btn-primary float-end d-inline-flex align-items-center gap-2" type="submit">
                <i data-lucide="save" class="pk-icon" aria-hidden="true"></i>
                <span>Simpan</span>
            </button>
        </div>
    </form>

    <form class="card mb-4" id="password-form" method="post" action="<?= esc(route('/user/post')) ?>">
        <div class="card-header p-4">
            <h2 class="h4 fw-bold mb-0 text-primary">Ubah Kata Sandi</h2>
        </div>

        <div class="card-body p-4">
            <input type="hidden" name="target" value="password">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold small" for="old-password">Kata Sandi Lama</label>
                    <input class="form-control" id="old-password" name="old_password" type="password" minlength="8" placeholder="********" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small" for="new-password">Kata Sandi Baru</label>
                    <input class="form-control" id="new-password" name="new_password" type="password" minlength="8" placeholder="********" required>
                </div>
            </div>
        </div>

        <div class="card-footer p-4">
            <button class="btn btn-primary float-end d-inline-flex align-items-center gap-2" type="submit">
                <i data-lucide="save" class="pk-icon" aria-hidden="true"></i>
                <span>Simpan</span>
            </button>
        </div>
    </form>
</main>

<div class="modal fade" id="avatarModal" data-endpoint="<?= esc(route('/user/post')) ?>" data-avatar="<?= $hasAvatar ? esc($avatarUrl) : '' ?>" tabindex="-1" aria-labelledby="avatarModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title h5 fw-bold text-primary" id="avatarModalTitle">Edit Foto Profil</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="pk-avatar-stage" id="avatarStage">
                    <div class="pk-avatar-crop" id="avatarCropperStage">
                        <img id="avatarCropperImage"<?= $hasAvatar ? ' src="' . esc($avatarUrl) . '"' : '' ?>>
                    </div>
                    <label class="pk-avatar-drop" id="avatarDrop" for="avatarFileInput"<?= $hasAvatar ? ' hidden' : '' ?>>
                        <span class="pk-avatar-drop-idle">
                            <i data-lucide="image-plus" class="pk-icon" aria-hidden="true"></i>
                            <span>Seret dan perbesar foto, lalu simpan. Hasil dipotong 1:1 dan dikompres ke WebP.</span>
                        </span>
                        <span class="pk-avatar-drop-hover">
                            <i data-lucide="upload" class="pk-icon" aria-hidden="true"></i>
                            <span>Lepaskan foto di sini</span>
                        </span>
                    </label>
                    <button type="button" class="pk-avatar-reset" id="avatarReset" aria-label="Batalkan foto"<?= $hasAvatar ? '' : ' hidden' ?>>
                        <i data-lucide="x" class="pk-icon" aria-hidden="true"></i>
                    </button>
                </div>
                <input type="file" id="avatarFileInput" class="visually-hidden" accept="image/jpeg,image/png,image/webp" tabindex="-1">
            </div>
            <div class="modal-footer justify-content-end">
                <button type="button" class="btn btn-outline-danger d-inline-flex align-items-center gap-2" id="avatarRemove"<?= $hasAvatar ? '' : ' hidden' ?>>
                    <i data-lucide="trash-2" class="pk-icon" aria-hidden="true"></i>
                    <span>Hapus</span>
                </button>
                <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-2" id="avatarSave"<?= $hasAvatar ? '' : ' disabled' ?>>
                    <i data-lucide="save" class="pk-icon" aria-hidden="true"></i>
                    <span>Simpan</span>
                </button>
            </div>
        </div>
    </div>
</div>

<?php Component::flush(); ?>
<script src="<?= asset('js/lib/cropper.min.js') ?>"></script>
<script src="<?= asset('js/profile.js') ?>"></script>
<?php $scripts = Component::endFlush(); ?>

<?php Component::include('user/footer', ['script' => $scripts]); ?>

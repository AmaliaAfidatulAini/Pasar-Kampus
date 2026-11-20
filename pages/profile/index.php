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
?>

<?php Component::flush(); ?>
<link rel="stylesheet" href="<?= asset("css/profile.css") ?>">
<?php $styles = Component::endFlush(); ?>

<?php Component::include('user/header', [
    'title' => 'Profil Pengguna',
    'styles' => $styles,
]); ?>

<main class="profile-page">
    <a class="profile-back" href="<?= esc(route('/')) ?>">
        <i data-lucide="arrow-left" aria-hidden="true"></i>
        <span>Kembali</span>
    </a>

    <section class="profile-card" aria-labelledby="profile-heading">
        <div class="profile-identity">
            <div>
                <input type="file" id="profile-avatar" name="avatar" form="profile-form" accept="image/jpeg,image/png,image/webp,image/gif" hidden>
                <button class="profile-avatar" type="button" id="profile-avatar-button" aria-label="Pilih foto profil">
                    <?php if ($avatarUrl !== ''): ?>
                        <img id="profile-avatar-preview" src="<?= esc($avatarUrl) ?>" alt="Foto profil <?= esc($profileName) ?>">
                    <?php else: ?>
                        <span id="profile-avatar-preview">Unggah Foto</span>
                    <?php endif; ?>
                </button>
            </div>
            <div>
                <h2><?= esc($profileName) ?></h2>
                <p><?= esc($profile['email']) ?></p>
            </div>
        </div>
    </section>

    <form class="profile-card" id="profile-form" method="post" action="<?= esc(route('/profile/post')) ?>" enctype="multipart/form-data">
        <input type="hidden" name="target" value="profile">
        <header>
            <h2>Informasi Akun</h2>
            <p>Sesuaikan preferensi dan biodata akun Anda.</p>
        </header>

        <div class="profile-fields">
            <div class="profile-field">
                <label for="profile-name">Nama Lengkap</label>
                <input id="profile-name" name="name" type="text" maxlength="255" value="<?= esc((string) ($profile['name'] ?? '')) ?>" required>
            </div>

            <div class="profile-field">
                <label for="profile-email">Email</label>
                <input id="profile-email" type="email" value="<?= esc((string) ($profile['email'] ?? '')) ?>" readonly>
            </div>

            <div class="profile-field">
                <label for="profile-phone">Nomor Telepon</label>
                <input id="profile-phone" name="phone" type="tel" maxlength="50" value="<?= esc((string) ($profile['phone'] ?? '')) ?>" placeholder="Nomor telepon">
            </div>

            <div class="profile-field">
                <label for="profile-campus">Asal Kampus</label>
                <input id="profile-campus" name="campus" type="text" maxlength="255" value="<?= esc((string) ($profile['campus'] ?? '')) ?>" placeholder="Nama kampus">
            </div>

            <div class="profile-field">
                <label for="profile-student-number">NIM</label>
                <input id="profile-student-number" name="student_number" type="text" maxlength="50" value="<?= esc((string) ($profile['student_number'] ?? '')) ?>" placeholder="Nomor induk mahasiswa">
            </div>

            <fieldset class="profile-field profile-field--full">
                <legend>Preferensi Kategori</legend>
                <div class="profile-preferences">
                    <?php foreach ($categories as $category): ?>
                        <label>
                            <input type="checkbox" name="preferences[]" value="<?= (int) $category['id'] ?>" <?= in_array((string) $category['id'], $selectedCategories, true) ? 'checked' : '' ?>>
                            <span><?= esc($category['name']) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </fieldset>

            <div class="profile-field">
                <label for="profile-subdistrict">Desa/Kelurahan</label>
                <input id="profile-subdistrict" name="subdistrict" type="text" maxlength="255" value="<?= esc((string) ($profile['subdistrict'] ?? '')) ?>" placeholder="Desa atau kelurahan">
            </div>

            <div class="profile-field">
                <label for="profile-district">Kecamatan</label>
                <input id="profile-district" name="district" type="text" maxlength="200" value="<?= esc((string) ($profile['district'] ?? '')) ?>" placeholder="Kecamatan">
            </div>
        </div>

        <footer>
            <button class="profile-button" type="submit">Simpan</button>
        </footer>
    </form>

    <form class="profile-card" id="password-form" method="post" action="<?= esc(route('/profile/post')) ?>">
        <input type="hidden" name="target" value="password">
        <header>
            <h2>Ubah Kata Sandi</h2>
            <p>Masukkan kata sandi lama sebelum membuat kata sandi baru.</p>
        </header>

        <div class="profile-fields">
            <div class="profile-field">
                <label for="old-password">Kata Sandi Lama</label>
                <input id="old-password" name="old_password" type="password" autocomplete="current-password" required>
            </div>
            <div class="profile-field">
                <label for="new-password">Kata Sandi Baru</label>
                <input id="new-password" name="new_password" type="password" minlength="8" autocomplete="new-password" required>
            </div>
        </div>

        <footer>
            <button class="profile-button" type="submit">Simpan</button>
        </footer>
    </form>
</main>

<?php Component::flush(); ?>
<script>
    const avatarInput = document.getElementById('profile-avatar');
    const avatarButton = document.getElementById('profile-avatar-button');
    avatarButton.addEventListener('click', () => avatarInput.click());
    avatarInput.addEventListener('change', () => {
        const file = avatarInput.files[0];
        if (!file) return;
        const image = document.createElement('img');
        image.src = URL.createObjectURL(file);
        image.alt = 'Pratinjau foto profil';
        avatarButton.replaceChildren(image);
    });
</script>
<?php $scripts = Component::endFlush(); ?>

<?php Component::include('user/footer', ['script' => $scripts]); ?>

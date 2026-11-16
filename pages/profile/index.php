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
<style>
    .profile-page {
        width: 100%;
        max-width: 1180px;
        margin: 0 auto;
        padding: 32px 24px 64px;
    }

    .profile-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 24px;
        color: #1D356D;
        font-weight: 700;
        text-decoration: none;

        &:hover,
        &:focus-visible {
            color: #2355B8;
        }
    }


    .profile-card {
        margin-bottom: 24px;
        padding: 32px;
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05);
    }

    .profile-card header {
        margin-bottom: 24px;
        padding-bottom: 24px;
        border-bottom: 1px solid #E2E8F0;

        h2 {
            font-size: 1.5rem;
            margin: 0;
            color: #0F172A;
        }

        p {
            margin: 6px 0 0;
            color: #64748B;
        }
    }

    .profile-identity {
        display: flex;
        align-items: center;
        gap: 24px;
    }

    .profile-avatar {
        display: grid;
        flex: 0 0 112px;
        place-items: center;
        width: 112px;
        height: 112px;
        padding: 0;
        overflow: hidden;
        color: #64748B;
        background: #F8FAFC;
        border: 1px solid #CBD5E1;
        border-radius: 50%;
        cursor: pointer;

        &:hover,
        &:focus-visible {
            border-color: #2355B8;
            outline: none;
        }
    
        img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        span {
            padding: 16px;
            text-align: center;
            font-size: 0.85rem;
            font-weight: 700;
        }
    }



    .profile-fields {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
    }

    .profile-fields > fieldset,
    .profile-fields > .profile-field--full {
        grid-column: 1 / -1;
    }

    .profile-field {
        display: flex;
        min-width: 0;
        flex-direction: column;
        gap: 7px;
        margin: 0;
        padding: 0;
        border: 0;

        label,
        legend {
            color: #0F172A;
            font-size: 0.9rem;
            font-weight: 700;
        }
        
        input,
        select {
            width: 100%;
            min-height: 44px;
            padding: 10px 13px;
            color: #1E293B;
            font: inherit;
            background: #FFFFFF;
            border: 1px solid #CBD5E1;
            border-radius: 8px;
            box-sizing: border-box;
        }
    }



    .profile-field input:focus,
    .profile-field select:focus,
    .profile-preferences input:focus-visible,
    .profile-button:focus-visible {
        border-color: #2355B8;
        outline: 3px solid rgba(35, 85, 184, 0.15);
        outline-offset: 1px;
    }

    .profile-field input[readonly],
    .profile-field select:disabled {
        color: #64748B;
        background: #F8FAFC;
        cursor: not-allowed;
    }

    .profile-preferences {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px 12px;
        margin: 0;
        padding: 0;
        border: 0;
    }

    .profile-preferences label {
        display: flex;
        align-items: center;
        gap: 10px;
        min-height: 44px;
        padding: 8px 12px;
        color: #1E293B;
        font-size: 0.9rem;
        font-weight: 500;
        border: 1px solid #CBD5E1;
        border-radius: 8px;
        cursor: pointer;
    }

    .profile-preferences input {
        width: 16px;
        height: 16px;
        accent-color: #1D356D;
    }

    .profile-card footer {
        display: flex;
        justify-content: flex-end;
        margin-top: 28px;
    }

    .profile-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 42px;
        padding: 10px 22px;
        color: #FFFFFF;
        font: inherit;
        font-weight: 700;
        background: #1D356D;
        border: 1px solid #1D356D;
        border-radius: 8px;
        cursor: pointer;
    }

    .profile-button:hover {
        background: #162850;
    }

    .profile-button:disabled {
        cursor: wait;
        opacity: 0.65;
    }


    @media (max-width: 700px) {
        .profile-page {
            padding: 24px 16px 48px;
        }

        .profile-card {
            padding: 24px 20px;
        }

        .profile-fields,
        .profile-preferences {
            grid-template-columns: 1fr;
        }

        .profile-identity {
            align-items: flex-start;
            flex-direction: column;
        }
    }
</style>
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

    <form class="profile-card" id="profile-form" method="post" action="<?= esc(route('/profile/update')) ?>" enctype="multipart/form-data">
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

    <form class="profile-card" id="password-form" method="post" action="<?= esc(route('/profile/update-password')) ?>">
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

<?php
$session = get_session();
$userName = $session['name'] ?? 'Pengguna';
$userEmail = $session['email'] ?? '';
$userCampus = $session['campus'] ?? '';
$userPhone = $session['phone'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PasarKampus - Profil Pengguna</title>
    <!-- Google Fonts Inter & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- CSS Stylesheet -->
    <link rel="stylesheet" href="<?= route('/css/style.css') ?>">

    <style>
        :root {
            --pk-nav-blue: #2355B8;
            --pk-navy: #1D356D;
            --pk-amber: #F59E0B;
            --pk-border: #E2E8F0;
            --pk-bg: #F8FAFC;
        }

        body.pk-profile-body {
            background-color: var(--pk-bg);
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
            color: #1E293B;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* 1. TOP NAVBAR */
        .pk-navbar {
            background: linear-gradient(180deg, #1D4ED8 0%, #2355B8 60%, #1E40AF 100%);
            height: 64px;
            display: flex;
            align-items: center;
            padding: 0 28px;
            color: #FFFFFF;
            position: sticky;
            top: 0;
            z-index: 900;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }

        .pk-nav-inner {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .pk-nav-logo {
            font-size: 1.35rem;
            font-weight: 800;
            color: #FFFFFF;
            text-decoration: none;
            letter-spacing: -0.5px;
            flex-shrink: 0;
        }

        .pk-nav-search {
            flex: 1;
            max-width: 420px;
            position: relative;
        }

        .pk-nav-search input {
            width: 100%;
            height: 38px;
            border-radius: 9999px;
            border: none;
            padding: 0 16px 0 38px;
            font-size: 0.85rem;
            outline: none;
            background: #FFFFFF;
            color: #1E293B;
            box-sizing: border-box;
            font-family: inherit;
        }

        .pk-nav-search input::placeholder {
            color: #94A3B8;
        }

        .pk-nav-search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748B;
            pointer-events: none;
            display: flex;
            align-items: center;
        }

        .pk-nav-menu {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .pk-nav-link {
            color: #FFFFFF;
            text-decoration: none;
            font-size: 0.88rem;
            font-weight: 600;
            transition: opacity 0.2s;
        }

        .pk-nav-link:hover {
            opacity: 0.85;
            text-decoration: underline;
        }

        /* 2. PROFILE CONTAINER */
        .pk-profile-container {
            flex: 1;
            width: 100%;
            max-width: 1060px;
            margin: 24px auto 48px;
            padding: 0 20px;
            box-sizing: border-box;
        }

        /* Kembali link */
        .pk-back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--pk-navy);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            margin-bottom: 22px;
            transition: color 0.15s, transform 0.15s;
        }

        .pk-back-btn:hover {
            color: #2563EB;
            transform: translateX(-3px);
        }

        /* Top Welcome & Photo Card */
        .pk-profile-card {
            background: #FFFFFF;
            border: 1px solid #EDF2F7;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            padding: 36px 42px;
            box-sizing: border-box;
            margin-bottom: 22px;
        }

        .pk-welcome-row {
            display: flex;
            align-items: center;
            gap: 40px;
        }

        .pk-avatar-upload-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex-shrink: 0;
        }

        .pk-avatar-circle {
            width: 136px;
            height: 136px;
            border-radius: 50%;
            border: 1.5px solid #E2E8F0;
            background-color: #F8FAFC;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            cursor: pointer;
            transition: border-color 0.2s, box-shadow 0.2s;
            position: relative;
        }

        .pk-avatar-circle:hover {
            border-color: #93C5FD;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.1);
        }

        .pk-avatar-circle img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
            display: none;
        }

        .pk-avatar-placeholder-text {
            color: #64748B;
            font-size: 0.92rem;
            font-weight: 600;
            line-height: 1.35;
            text-align: center;
            user-select: none;
        }

        .pk-avatar-link-text {
            margin-top: 14px;
            color: #1D4ED8;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: color 0.15s;
        }

        .pk-avatar-link-text:hover {
            color: #1E3A8A;
            text-decoration: underline;
        }

        .pk-welcome-text h1 {
            font-size: 2.1rem;
            font-weight: 800;
            color: #0F172A;
            margin: 0 0 6px 0;
            letter-spacing: -0.5px;
            line-height: 1.2;
        }

        .pk-welcome-text h2 {
            font-size: 1.9rem;
            font-weight: 800;
            color: #0F172A;
            margin: 0;
            letter-spacing: -0.3px;
            line-height: 1.2;
        }

        /* Bottom Form Details Card */
        .pk-form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            column-gap: 40px;
            row-gap: 24px;
        }

        .pk-form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .pk-form-label {
            font-size: 1rem;
            font-weight: 500;
            color: #0F172A;
        }

        .pk-form-input {
            width: 100%;
            height: 46px;
            border: 1.5px solid #CBD5E1;
            border-radius: 12px;
            padding: 0 16px;
            font-size: 0.92rem;
            font-family: inherit;
            color: #1E293B;
            background: #FFFFFF;
            box-sizing: border-box;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .pk-form-input::placeholder {
            color: #94A3B8;
        }

        .pk-form-input:focus {
            border-color: #2563EB;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }

        /* Action Simpan Button Row */
        .pk-action-row {
            display: flex;
            justify-content: flex-end;
            margin-top: 24px;
        }

        .pk-btn-simpan {
            background-color: var(--pk-navy);
            color: #FFFFFF;
            border: none;
            border-radius: 9999px;
            padding: 12px 42px;
            font-size: 1rem;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
            box-shadow: 0 4px 14px rgba(29, 53, 109, 0.2);
        }

        .pk-btn-simpan:hover {
            background-color: #162850;
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(29, 53, 109, 0.3);
        }

        .pk-btn-simpan:active {
            transform: translateY(1px);
        }

        /* Toast Feedback */
        .pk-toast {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #10B981;
            color: #FFFFFF;
            padding: 14px 24px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.92rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            gap: 10px;
            z-index: 10000;
            opacity: 0;
            transform: translateY(20px);
            pointer-events: none;
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        .pk-toast.show {
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
        }

        @media (max-width: 768px) {
            .pk-welcome-row {
                flex-direction: column;
                text-align: center;
                gap: 20px;
            }
            .pk-form-grid {
                grid-template-columns: 1fr;
                row-gap: 18px;
            }
            .pk-profile-card {
                padding: 24px 20px;
            }
            .pk-nav-search {
                max-width: 200px;
            }
        }
    </style>
</head>
<body class="pk-profile-body">

    <!-- ================= 1. TOP NAVBAR ================= -->
    <header class="pk-navbar">
        <div class="pk-nav-inner">
            <!-- Brand -->
            <a href="<?= route('/') ?>" class="pk-nav-logo">
                PasarKampus
            </a>

            <!-- Search Bar -->
            <div class="pk-nav-search">
                <span class="pk-nav-search-icon">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </span>
                <input type="text" id="profileSearchInput" placeholder="Cari barang...." onkeydown="if(event.key==='Enter') window.location.href='<?= route('/dashboard.php') ?>?q='+encodeURIComponent(this.value)">
            </div>

            <!-- Right Links & Avatar Menu -->
            <div class="pk-nav-menu">
                <a href="<?= route('/riwayat.php') ?>" class="pk-nav-link">Riwayat</a>
                <a href="<?= route('/dashboard.php') ?>" class="pk-nav-link">Katalog</a>
                <a href="<?= route('/#faq') ?>" class="pk-nav-link">butuh bantuan?</a>
            </div>
        </div>
    </header>

    <!-- ================= 2. MAIN PROFILE CONTENT ================= -->
    <main class="pk-profile-container">

        <!-- Back Button -->
        <a href="javascript:void(0)" onclick="goBack()" class="pk-back-btn">
            &larr; Kembali
        </a>

        <!-- Top Card: Avatar Upload & Welcome Greeting -->
        <section class="pk-profile-card">
            <div class="pk-welcome-row">
                <!-- Avatar Upload Circle -->
                <div class="pk-avatar-upload-box">
                    <input type="file" id="profileFileInput" accept="image/*" style="display: none;" onchange="handleImageUpload(event)">
                    <div class="pk-avatar-circle" id="avatarCircleBtn" onclick="triggerFileUpload()" title="Klik untuk unggah foto">
                        <img id="avatarPreviewImg" alt="Foto Profil">
                        <div class="pk-avatar-placeholder-text" id="avatarPlaceholderText">
                            Unggah<br>foto
                        </div>
                    </div>
                    <span class="pk-avatar-link-text" onclick="triggerFileUpload()">Unggah foto profil</span>
                </div>

                <!-- Welcome Text -->
                <div class="pk-welcome-text">
                    <h1>Selamat datang!</h1>
                    <h2 id="profileUsername">user</h2>
                </div>
            </div>
        </section>

        <!-- Bottom Card: Form Biodata -->
        <section class="pk-profile-card">
            <form id="profileForm" onsubmit="saveProfileData(event)">
                <div class="pk-form-grid">
                    <!-- Left Column -->
                    <div class="pk-form-group">
                        <label class="pk-form-label" for="inputJenisKelamin">Jenis Kelamin</label>
                        <input type="text" id="inputJenisKelamin" class="pk-form-input" placeholder="Masukkan jenis kelamin">
                    </div>

                    <!-- Right Column -->
                    <div class="pk-form-group">
                        <label class="pk-form-label" for="inputTanggalLahir">Tanggal Lahir (dd/mm/yy)</label>
                        <input type="text" id="inputTanggalLahir" class="pk-form-input" placeholder="Masukkan tanggal lahir">
                    </div>

                    <!-- Left Column -->
                    <div class="pk-form-group">
                        <label class="pk-form-label" for="inputAlamat">Alamat lengkap</label>
                        <input type="text" id="inputAlamat" class="pk-form-input" placeholder="Masukkan alamat">
                    </div>

                    <!-- Right Column -->
                    <div class="pk-form-group">
                        <label class="pk-form-label" for="inputKelurahan">Kelurahan</label>
                        <input type="text" id="inputKelurahan" class="pk-form-input" placeholder="Masukkan kelurahan">
                    </div>
                </div>
            </form>
        </section>

        <!-- Right Aligned Simpan Button -->
        <div class="pk-action-row">
            <button type="button" class="pk-btn-simpan" onclick="saveProfileData()">Simpan</button>
        </div>

    </main>

    <!-- Toast Notification -->
    <div id="saveToast" class="pk-toast">
        <span>✅</span>
        <span>Perubahan profil berhasil disimpan!</span>
    </div>

    <!-- ================= 3. FOOTER ================= -->
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
                    &copy; 2026 PasarKampus. Hak Cipta Dilindungi.
                </div>
                <div>
                    Dibuat untuk Project Based Learning (PBL) Mahasiswa.
                </div>
            </div>
        </div>
    </footer>

    <!-- JavaScript logic -->
    <script>
        let currentUploadedPhoto = '';

        function triggerFileUpload() {
            const input = document.getElementById('profileFileInput');
            if (input) input.click();
        }

        function handleImageUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(e) {
                currentUploadedPhoto = e.target.result;
                const img = document.getElementById('avatarPreviewImg');
                const placeholder = document.getElementById('avatarPlaceholderText');
                if (img) {
                    img.src = currentUploadedPhoto;
                    img.style.display = 'block';
                }
                if (placeholder) {
                    placeholder.style.display = 'none';
                }
            };
            reader.readAsDataURL(file);
        }

        function loadProfileData() {
            const raw = localStorage.getItem('preloved_user');
            if (raw) {
                try {
                    const u = JSON.parse(raw);
                    // Display name
                    const name = u.nama || u.username || 'user';
                    const usernameEl = document.getElementById('profileUsername');
                    if (usernameEl) {
                        usernameEl.innerText = name;
                    }

                    // Form fields
                    if (u.jenisKelamin) document.getElementById('inputJenisKelamin').value = u.jenisKelamin;
                    if (u.tanggalLahir) document.getElementById('inputTanggalLahir').value = u.tanggalLahir;
                    if (u.alamat) document.getElementById('inputAlamat').value = u.alamat;
                    if (u.kelurahan) document.getElementById('inputKelurahan').value = u.kelurahan;

                    // Photo
                    if (u.foto && u.foto.trim() !== '') {
                        currentUploadedPhoto = u.foto;
                        const img = document.getElementById('avatarPreviewImg');
                        const placeholder = document.getElementById('avatarPlaceholderText');
                        if (img) {
                            img.src = u.foto;
                            img.style.display = 'block';
                        }
                        if (placeholder) {
                            placeholder.style.display = 'none';
                        }
                    }
                } catch (e) {
                    console.error('Error parsing preloved_user', e);
                }
            }
        }

        function saveProfileData(event) {
            if (event) event.preventDefault();

            let u = {};
            const raw = localStorage.getItem('preloved_user');
            if (raw) {
                try {
                    u = JSON.parse(raw);
                } catch (e) {
                    u = {};
                }
            }

            // Update fields
            u.jenisKelamin = document.getElementById('inputJenisKelamin').value.trim();
            u.tanggalLahir = document.getElementById('inputTanggalLahir').value.trim();
            u.alamat = document.getElementById('inputAlamat').value.trim();
            u.kelurahan = document.getElementById('inputKelurahan').value.trim();

            if (currentUploadedPhoto) {
                u.foto = currentUploadedPhoto;
            }

            localStorage.setItem('preloved_user', JSON.stringify(u));
            localStorage.setItem('pk_logged_in', 'true');

            // Show Toast
            const toast = document.getElementById('saveToast');
            if (toast) {
                toast.classList.add('show');
                setTimeout(() => {
                    toast.classList.remove('show');
                }, 3000);
            }
        }

        function goBack() {
            if (window.history.length > 1) {
                window.history.back();
            } else {
                window.location.href = '<?= route('/dashboard.php') ?>';
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadProfileData();
        });
    </script>
</body>
</html>

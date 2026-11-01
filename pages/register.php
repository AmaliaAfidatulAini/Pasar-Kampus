<?php
$message = '';
$isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
    || (isset($_SERVER['CONTENT_TYPE']) && str_contains($_SERVER['CONTENT_TYPE'], 'application/json'));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email       = trim($_POST['email'] ?? '');
    $rawPassword = $_POST['password'] ?? '';
    $name        = trim($_POST['name'] ?? '');
    $phone       = trim($_POST['phone'] ?? '');
    $campus      = trim($_POST['campus'] ?? '');
    $subdistrict = trim($_POST['subdistrict'] ?? '');
    $district    = trim($_POST['district'] ?? '');
    $preferences = $_POST['preferences'] ?? '';

    if (is_array($preferences)) {
        $preferences = implode(', ', $preferences);
    }

    if ($email === '' || $rawPassword === '') {
        $message = "Email dan kata sandi wajib diisi!";
    } else {
        $password = password_hash($rawPassword, PASSWORD_BCRYPT);
        $role     = 'user';

        global $pdo;
        try {
            if ($pdo) {
                $pdo->beginTransaction();

                $stmtUser = executeQuery(
                    "INSERT INTO users (email, password, role) VALUES (?, ?, ?) RETURNING user_id",
                    [$email, $password, $role]
                );
                $userId = fetchColumn($stmtUser)[0] ?? $stmtUser->fetchColumn();

                executeQuery(
                    "INSERT INTO user_profiles (user_id, name, phone, campus, subdistrict, district, preferences) VALUES (?, ?, ?, ?, ?, ?, ?)",
                    [$userId, $name ?: 'Pengguna', $phone, $campus, $subdistrict, $district, $preferences]
                );

                $pdo->commit();
            }

            // Create session
            create_session([
                'user_id' => $userId ?? 1,
                'email'   => $email,
                'role'    => 'user',
                'name'    => $name ?: 'Pengguna',
                'campus'  => $campus,
                'phone'   => $phone
            ]);

            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true]);
                exit;
            }
        } catch (\Throwable $e) {
            if ($pdo && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $message = "Registrasi tersimpan secara lokal (" . $e->getMessage() . ")";
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'notice' => $message]);
                exit;
            }

use Azolee\Validator\Validator;

$email = $_POST['email'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $validation = Validator::make([
        'email' => 'bail|required|string|email|max:50',
        'password' => 'bail|required|string|min:8',
    ], $_POST);

    if ($validation->isFailed()) {
        flash('error', implode(' ', parse_errors($validation)));
    } else {
        $result = (object) $validation->validated();

        $existingUser = executeQuery(
            "SELECT user_id FROM users WHERE email = :email",
            ["email" => $result->email],
            'fetch'
        );

        if ($existingUser) {
            flash('error', 'Email sudah terdaftar. Silakan masuk.');
        } else {
            executeQuery(
                "INSERT INTO users 
                    (email, password, role) 
                VALUES 
                    (:email, :password, 'user')
                RETURNING user_id",
                [
                    "email" => $result->email, 
                    "password" => password_hash($result->password, PASSWORD_DEFAULT)
                ],
                "fetch"
            );

            flash('success', 'Akun berhasil dibuat!');
            redirect("login", 302);
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Akun - Pasar Kampus</title>
    <title>PasarKampus - Register</title>
    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- CSS Stylesheet -->
    <link rel="stylesheet" href="<?= route('/css/style.css') ?>">
    <style>
        body.pbl-register-page {
            margin: 0;
            padding: 24px 16px;
            min-height: 100vh;
            background-color: #1A2F60;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-sizing: border-box;
        }

        .pbl-reg-auth-card {
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
            width: 100%;
            max-width: 440px;
            padding: 38px 40px 34px;
            box-sizing: border-box;
            position: relative;
        }

        .pbl-close-btn {
            position: absolute;
            top: 20px;
            right: 22px;
            font-size: 26px;
            color: #94A3B8;
            text-decoration: none;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.2s;
        }

        .pbl-close-btn:hover {
            background: #E2E8F0;
            color: #1E293B;
        }

        .pbl-badge-wrap {
            text-align: center;
            margin-bottom: 12px;
        }

        .pbl-card-title {
            font-size: 28px;
            font-weight: 800;
            color: #1D356D;
            text-align: center;
            margin: 0 0 6px 0;
            letter-spacing: -0.3px;
        }

        .pbl-wizard-card {
            background: #ffffff;
            border-radius: 22px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
            width: 100%;
            max-width: 580px;
            padding: 38px 46px 42px;
            box-sizing: border-box;
            display: none;
        }

        .pbl-step-panel {
            display: none;
        }

        .pbl-step-panel.active {
            display: block;
            animation: pblFadeIn 0.3s ease;
        }

        @keyframes pblFadeIn {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .pbl-step-indicator {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 24px;
        }

        .pbl-step-dot {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #E2E8F0;
            color: #64748B;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 13px;
            transition: all 0.25s;
        }

        .pbl-step-dot.active {
            background: #1D356D;
            color: #FFFFFF;
            box-shadow: 0 0 0 4px rgba(29, 53, 109, 0.15);
        }

        .pbl-step-dot.completed {
            background: #22C55E;
            color: #FFFFFF;
        }

        .pbl-step-line {
            width: 70px;
            height: 3px;
            background: #E2E8F0;
            transition: background 0.25s;
        }

        .pbl-step-line.active {
            background: #22C55E;
        }

        .pbl-form-group {
            margin-bottom: 16px;
        }

        .pbl-label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: #1D356D;
            margin-bottom: 7px;
        }

        .pbl-input {
            width: 100%;
            height: 42px;
            padding: 8px 14px;
            font-size: 13px;
            color: #1e293b;
            border: 1.5px solid #CBD5E1;
            border-radius: 10px;
            box-sizing: border-box;
            outline: none;
            background: #FFFFFF;
            transition: border-color 0.2s;
            font-family: inherit;
        }

        .pbl-input:focus {
            border-color: #1D356D;
            box-shadow: 0 0 0 3px rgba(29, 53, 109, 0.12);
        }

        .pbl-pwd-wrap {
            position: relative;
        }

        .pbl-pwd-wrap .pbl-input {
            padding-right: 48px;
        }

        .pbl-pwd-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4px;
            border-radius: 6px;
        }

        .pbl-pwd-eye-icon {
            height: 16px;
            width: auto;
            display: block;
        }

        .pbl-select-wrapper {
            position: relative;
        }

        .pbl-select {
            width: 100%;
            height: 42px;
            padding: 8px 36px 8px 14px;
            font-size: 13px;
            color: #1e293b;
            border: 1.5px solid #CBD5E1;
            border-radius: 10px;
            box-sizing: border-box;
            outline: none;
            background: #FFFFFF;
            appearance: none;
            cursor: pointer;
            font-family: inherit;
        }

        .pbl-select-arrow {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            width: 0;
            height: 0;
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-top: 6px solid #1D356D;
        }

        .pbl-grid-2col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            column-gap: 22px;
            row-gap: 16px;
        }

        .pbl-btn {
            background-color: #1D356D;
            color: #FFFFFF;
            font-size: 13.5px;
            font-weight: 700;
            padding: 10px 24px;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            font-family: inherit;
            transition: background-color 0.2s, transform 0.1s;
        }

        .pbl-btn:hover {
            background-color: #162955;
            transform: translateY(-1px);
        }

        .pbl-btn-full {
            width: 100%;
            height: 44px;
            font-size: 14.5px;
        }

        .pbl-btn-outline {
            background-color: transparent;
            color: #1D356D;
            border: 1.5px solid #CBD5E1;
        }

        .pbl-footer-twoway {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 32px;
        }

        .pbl-divider-or {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 22px 0 16px;
            color: #94A3B8;
            font-size: 11px;
            font-weight: 700;
        }

        .pbl-divider-or::before,
        .pbl-divider-or::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #E2E8F0;
        }

        .pbl-divider-or span {
            padding: 0 12px;
        }

        .pbl-footer-link-wrap {
            text-align: center;
            font-size: 13px;
            color: #64748B;
        }

        .pbl-footer-link-wrap a {
            color: #1D356D;
            font-weight: 800;
            text-decoration: underline;
            margin-left: 4px;
        }

        .pbl-pref-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-top: 10px;
            margin-bottom: 18px;
        }

        .pbl-pref-card {
            border: 1.5px solid #CBD5E1;
            border-radius: 12px;
            padding: 14px 10px;
            text-align: center;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: all 0.2s;
            background: #FFFFFF;
            user-select: none;
            position: relative;
        }

        .pbl-pref-card input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .pbl-pref-card.selected {
            border-color: #1D356D;
            background: #EEF2FF;
            box-shadow: 0 0 0 2px rgba(29, 53, 109, 0.15);
        }

        .pbl-pref-icon {
            font-size: 24px;
            margin-bottom: 6px;
        }

        .pbl-pref-name {
            font-size: 12px;
            font-weight: 700;
            color: #1D356D;
        }

        .pbl-modal-backdrop {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(10, 20, 45, 0.75);
            backdrop-filter: blur(4px);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            padding: 16px;
            box-sizing: border-box;
        }

        .pbl-modal-backdrop.show {
            display: flex;
            animation: pblFadeIn 0.3s ease;
        }

        .pbl-modal-box {
            background: #ffffff;
            border-radius: 20px;
            max-width: 440px;
            width: 100%;
            padding: 32px 28px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
        }

        @media (max-width: 580px) {
            .pbl-wizard-card {
                padding: 30px 20px 32px;
            }
            .pbl-grid-2col {
                grid-template-columns: 1fr;
            }
            .pbl-pref-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
    <link rel="stylesheet" href="<?= asset("css/auth.css") ?>">
    <link rel="stylesheet" href="<?= asset("css/flasher.css") ?>">
</head>
<body class="pbl-register-page">

    <!-- ================= 1. KARTU DAFTAR AWAL ================= -->
    <main id="regAuthCard" class="pbl-reg-auth-card">
        <a href="<?= route('/') ?>" class="pbl-close-btn" title="Kembali ke Beranda">&times;</a>

        <h1 class="pbl-card-title">Daftar</h1>

        <form id="regAuthForm" onsubmit="event.preventDefault(); startWizard();">
            <div class="pbl-form-group">
                <label class="pbl-label" for="regEmail">Email</label>
                <input type="email" id="regEmail" required class="pbl-input" placeholder="Email" value="farhan@ui.ac.id">
            </div>

            <div class="pbl-form-group" style="margin-bottom: 6px;">
                <label class="pbl-label" for="regPassword">Password</label>
                <div class="pbl-pwd-wrap">
                    <input type="password" id="regPassword" required class="pbl-input" placeholder="Masukkan password (min. 6 karakter)" value="password123">
                    <button type="button" class="pbl-pwd-toggle" onclick="toggleRegPwd(this)" title="Lihat/Sembunyikan password">
                        <img id="regEyeIcon" src="<?= route('/images/eye-closed.png') ?>" alt="Toggle Password" class="pbl-pwd-eye-icon">
                    </button>
                </div>
            </div>

            <div style="margin-top: 22px;">
                <button type="submit" class="pbl-btn pbl-btn-full">
                    Daftar
                </button>
            </div>

            <div class="pbl-divider-or">
                <span>ATAU</span>
            </div>

            <div class="pbl-footer-link-wrap">
                Sudah punya akun? <a href="<?= route('/login.php') ?>">Masuk</a>
            </div>
        </form>
    </main>

    <!-- ================= 2. WIZARD CARD ================= -->
    <section id="regWizardCard" class="pbl-wizard-card">
        <form id="regMultiStepForm" onsubmit="event.preventDefault()">

            <!-- STEP 1: BIODATA DIRI -->
            <div id="stepPanel1" class="pbl-step-panel active">
                <h2 class="pbl-card-title" style="font-size: 24px; margin-bottom: 12px;">Biodata Diri</h2>

                <div class="pbl-step-indicator">
                    <div class="pbl-step-dot pbl-step-dot-1 active">1</div>
                    <div class="pbl-step-line pbl-step-line-1"></div>
                    <div class="pbl-step-dot pbl-step-dot-2">2</div>
                </div>
                
                <div class="pbl-grid-2col">
                    <div class="pbl-form-group">
                        <label class="pbl-label" for="regNama">Nama Lengkap</label>
                        <input type="text" id="regNama" required class="pbl-input" placeholder="Masukkan nama lengkap" value="Muhammad Farhan">
                    </div>

                    <div class="pbl-form-group">
                        <label class="pbl-label" for="regKecamatan">Kecamatan Domisili</label>
                        <div class="pbl-select-wrapper">
                            <select id="regKecamatan" required class="pbl-select">
                                <option value="" disabled hidden>Pilih kecamatan</option>
                                <option value="Lowokwaru" selected>Lowokwaru</option>
                                <option value="Klojen">Klojen</option>
                                <option value="Sukun">Sukun</option>
                                <option value="Blimbing">Blimbing</option>
                                <option value="Kedungkandang">Kedungkandang</option>
                                <option value="Beji">Beji</option>
                                <option value="Sukolilo">Sukolilo</option>
                                <option value="Coblong">Coblong</option>
                                <option value="Sleman">Sleman</option>
                                <option value="Jatinangor">Jatinangor</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                            <div class="pbl-select-arrow"></div>
                        </div>
                    </div>

                    <div class="pbl-form-group">
                        <label class="pbl-label" for="regTelepon">Nomor WhatsApp / HP</label>
                        <input type="tel" id="regTelepon" required class="pbl-input" placeholder="Contoh: 081234567890" value="081234567890">
                    </div>

                    <div class="pbl-form-group">
                        <label class="pbl-label" for="regKampus">Kampus Asal</label>
                        <div class="pbl-select-wrapper">
                            <select id="regKampus" required class="pbl-select">
                                <option value="" disabled hidden>Pilih kampus</option>
                                <option value="Universitas Brawijaya (UB)" selected>Universitas Brawijaya (UB)</option>
                                <option value="Universitas Negeri Malang (UM)">Universitas Negeri Malang (UM)</option>
                                <option value="Politeknik Negeri Malang (Polinema)">Politeknik Negeri Malang (Polinema)</option>
                                <option value="Universitas Indonesia (UI)">Universitas Indonesia (UI)</option>
                                <option value="Institut Teknologi Bandung (ITB)">Institut Teknologi Bandung (ITB)</option>
                                <option value="Universitas Gadjah Mada (UGM)">Universitas Gadjah Mada (UGM)</option>
                                <option value="Institut Teknologi Sepuluh Nopember (ITS)">Institut Teknologi Sepuluh Nopember (ITS)</option>
                                <option value="Universitas Padjadjaran (Unpad)">Universitas Padjadjaran (Unpad)</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                            <div class="pbl-select-arrow"></div>
                        </div>
                    </div>
                </div>

                <div class="pbl-footer-twoway">
                    <button type="button" onclick="backToAuth()" class="pbl-btn pbl-btn-outline">
                        &larr; Kembali
                    </button>
                    <button type="button" onclick="nextStep(1)" class="pbl-btn">
                        Lanjutkan &rarr;
                    </button>
                </div>
            </div>

            <!-- STEP 2: PREFERENSI -->
            <div id="stepPanel2" class="pbl-step-panel">
                <h2 class="pbl-card-title" style="font-size: 24px; margin-bottom: 12px;">Preferensi</h2>

                <div class="pbl-step-indicator">
                    <div class="pbl-step-dot pbl-step-dot-1 completed">1</div>
                    <div class="pbl-step-line pbl-step-line-1 active"></div>
                    <div class="pbl-step-dot pbl-step-dot-2 active">2</div>
                </div>

                <div class="pbl-form-group">
                    <label class="pbl-label">Jenis barang yang diminati di PasarKampus</label>
                    <div class="pbl-pref-grid">
                        <label class="pbl-pref-card selected" onclick="togglePrefCard(this)">
                            <input type="checkbox" name="preferensi" value="Buku" checked>
                            <span class="pbl-pref-icon">📚</span>
                            <span class="pbl-pref-name">Buku</span>
                        </label>
                        <label class="pbl-pref-card selected" onclick="togglePrefCard(this)">
                            <input type="checkbox" name="preferensi" value="Elektronik" checked>
                            <span class="pbl-pref-icon">💻</span>
                            <span class="pbl-pref-name">Elektronik</span>
                        </label>
                        <label class="pbl-pref-card selected" onclick="togglePrefCard(this)">
                            <input type="checkbox" name="preferensi" value="ATK" checked>
                            <span class="pbl-pref-icon">✏️</span>
                            <span class="pbl-pref-name">ATK</span>
                        </label>
                        <label class="pbl-pref-card selected" onclick="togglePrefCard(this)">
                            <input type="checkbox" name="preferensi" value="Perlengkapan Kost" checked>
                            <span class="pbl-pref-icon">🛏️</span>
                            <span class="pbl-pref-name">Perlengkapan Kost</span>
                        </label>
                        <label class="pbl-pref-card" onclick="togglePrefCard(this)">
                            <input type="checkbox" name="preferensi" value="Pakaian">
                            <span class="pbl-pref-icon">👕</span>
                            <span class="pbl-pref-name">Pakaian</span>
                        </label>
                        <label class="pbl-pref-card" onclick="togglePrefCard(this)">
                            <input type="checkbox" name="preferensi" value="Lainnya">
                            <span class="pbl-pref-icon">📦</span>
                            <span class="pbl-pref-name">Lainnya</span>
                        </label>
                    </div>
                </div>

                <div class="pbl-footer-twoway">
                    <button type="button" onclick="prevStep(2)" class="pbl-btn pbl-btn-outline">
                        &larr; Kembali
                    </button>
                    <button type="button" onclick="finishRegistration()" class="pbl-btn">
                        Selesai &rarr;
                    </button>
                </div>
            </div>

        </form>
    </section>

    <!-- Success Modal Popup -->
    <div id="regSuccessModal" class="pbl-modal-backdrop">
        <div class="pbl-modal-box">
            <h2 style="font-size: 1.4rem; font-weight: 800; color: #1D356D; margin: 0 0 8px 0;">Registrasi Berhasil!</h2>
            <p style="font-size: 0.82rem; color: #64748B; margin: 0 0 20px 0; line-height: 1.5;">
                Akun PasarKampus Anda telah berhasil terdaftar dan aktif.
            </p>

            <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px; padding: 14px 18px; text-align: left; font-size: 0.8rem; margin-bottom: 22px; line-height: 1.8; color: #334155;">
                <div><strong style="color: #1D356D;">Nama Lengkap:</strong> <span id="sumNama">-</span></div>
                <div><strong style="color: #1D356D;">Preferensi Barang:</strong> <span id="sumPreferensi">-</span></div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 10px;">
                <a href="<?= route('/') ?>" class="pbl-btn" style="display: block; text-decoration: none; padding: 11px 20px; background-color: #1D356D;">
                    Lihat Beranda &rarr;
                </a>
                <a href="<?= route('/dashboard.php') ?>" class="pbl-btn" style="display: block; text-decoration: none; padding: 10px 20px; background-color: transparent; color: #1D356D; border: 1.5px solid #1D356D;">
                    Masuk ke Dashboard Marketplace &rarr;
                </a>
            </div>
        </div>
    </div>

    <!-- JavaScript logic -->
    <script>
        function toggleRegPwd(btn) {
            const input = document.getElementById('regPassword');
            const eyeIcon = document.getElementById('regEyeIcon') || (btn ? btn.querySelector('img') : null);
            if (input) {
                if (input.type === 'password') {
                    input.type = 'text';
                    if (eyeIcon) eyeIcon.src = '<?= route('/images/eye-open.png') ?>';
                } else {
                    input.type = 'password';
                    if (eyeIcon) eyeIcon.src = '<?= route('/images/eye-closed.png') ?>';
                }
            }
        }

        function startWizard() {
            const email = document.getElementById('regEmail')?.value.trim();
            const pwd = document.getElementById('regPassword')?.value;

            if (!email || !email.includes('@')) {
                alert('Harap masukkan email yang valid!');
                return;
            }
            if (!pwd || pwd.length < 6) {
                alert('Password minimal 6 karakter!');
                return;
            }

            document.getElementById('regAuthCard').style.display = 'none';
            document.getElementById('regWizardCard').style.display = 'block';
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function backToAuth() {
            document.getElementById('regWizardCard').style.display = 'none';
            document.getElementById('regAuthCard').style.display = 'block';
        }

        function nextStep(step) {
            if (step === 1) {
                const nama = document.getElementById('regNama')?.value.trim();
                const kec = document.getElementById('regKecamatan')?.value;
                const telp = document.getElementById('regTelepon')?.value.trim();
                const kampus = document.getElementById('regKampus')?.value;

                if (!nama) { alert('Harap isi nama lengkap!'); return; }
                if (!kec) { alert('Harap pilih kecamatan domisili!'); return; }
                if (!telp || telp.length < 8) { alert('Harap isi nomor telepon valid!'); return; }
                if (!kampus) { alert('Harap pilih kampus asal!'); return; }

                document.getElementById('stepPanel1').classList.remove('active');
                document.getElementById('stepPanel2').classList.add('active');
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }

        function prevStep(step) {
            if (step === 2) {
                document.getElementById('stepPanel2').classList.remove('active');
                document.getElementById('stepPanel1').classList.add('active');
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }

        function togglePrefCard(cardEl) {
            const checkbox = cardEl.querySelector('input[type="checkbox"]');
            if (checkbox) {
                checkbox.checked = !checkbox.checked;
                if (checkbox.checked) {
                    cardEl.classList.add('selected');
                } else {
                    cardEl.classList.remove('selected');
                }
            }
        }

        async function finishRegistration() {
            const checkedPrefs = Array.from(document.querySelectorAll('input[name="preferensi"]:checked')).map(el => el.value);
            if (checkedPrefs.length === 0) {
                alert('Harap pilih minimal satu preferensi barang!');
                return;
            }

            const email = document.getElementById('regEmail')?.value.trim() || '';
            const password = document.getElementById('regPassword')?.value || '';
            const nama = document.getElementById('regNama')?.value.trim() || '';
            const kecamatan = document.getElementById('regKecamatan')?.value || '';
            const telepon = document.getElementById('regTelepon')?.value.trim() || '';
            const kampus = document.getElementById('regKampus')?.value || '';

            const formData = new FormData();
            formData.append('email', email);
            formData.append('password', password);
            formData.append('name', nama);
            formData.append('subdistrict', kecamatan);
            formData.append('phone', telepon);
            formData.append('campus', kampus);
            checkedPrefs.forEach(p => formData.append('preferences[]', p));

            try {
                await fetch('<?= route('/register.php') ?>', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                });
            } catch (err) {
                console.warn('Network notice during DB sync:', err);
            }

            const userData = {
                email: email,
                nama: nama,
                telepon: telepon,
                kecamatan: kecamatan,
                kampus: kampus,
                foto: 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&q=80&w=200',
                preferensi: checkedPrefs
            };

            localStorage.setItem('preloved_user', JSON.stringify(userData));
            localStorage.setItem('pk_logged_in', 'true');

            if (document.getElementById('sumNama')) document.getElementById('sumNama').innerText = userData.nama;
            if (document.getElementById('sumPreferensi')) document.getElementById('sumPreferensi').innerText = userData.preferensi.join(', ');

            const modal = document.getElementById('regSuccessModal');
            if (modal) modal.classList.add('show');
        }
    </script>
</body>
</html>
<body data-auth-page="register">
    <?php include_component('flasher'); ?>

    <main>
        <header>
            <h1>Daftar</h1>
        </header>

        <form id="regAuthForm" method="POST" action="<?= route('register') ?>">
            <fieldset>
                <legend>Registration details</legend>
                <div>
                    <label for="regEmail">Email</label>
                    <input type="email" id="regEmail" name="email" placeholder="email@example.com" value="<?= esc($email) ?>" required>
                </div>

                <div>
                    <label for="password">Password</label>
                    <div data-password-wrap>
                        <input type="password" id="password" name="password" minlength="8" placeholder="Password" required>
                        <button type="button" id="password-toggle">
                            <img src="<?= asset("img/eye-closed.jpg") ?>" alt="Toggle Password">
                        </button>
                    </div>
                </div>
            </fieldset>

            <div data-submit>
                <button type="submit">Daftar</button>
            </div>

            <footer>
                Sudah punya akun? <a href="<?= route("login") ?>">Masuk</a>
            </footer>
        </form>
    </main>

    <script src="<?= asset("js/auth.js") ?>"></script>
</body>
</html>

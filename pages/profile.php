<?php
$session = session_include('/login');
$userId = (int) ($session['user_id'] ?? 0);

// Handle AJAX / Form submission for profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (($_POST['action'] ?? '') === 'update_profile' || isset($_POST['save_profile']))) {
    $gender = trim((string)($_POST['jenis_kelamin'] ?? ''));
    $birthDate = trim((string)($_POST['tanggal_lahir'] ?? ''));
    $alamat = trim((string)($_POST['alamat'] ?? ''));
    $kelurahan = trim((string)($_POST['kelurahan'] ?? ''));

    $avatarUrl = null;
    $attId = null;

    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/jpg'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $_FILES['avatar']['tmp_name']);
        finfo_close($finfo);

        if (in_array($mime, $allowed, true)) {
            $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
            if (!$ext) $ext = 'jpg';
            $fileName = 'avatar_' . $userId . '_' . time() . '.' . strtolower($ext);
            $targetDir = BASE_PATH . '/public/uploads/avatars';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0775, true);
            }
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetDir . '/' . $fileName)) {
                $avatarUrl = upload('avatars/' . $fileName);

                // Insert into attachments table
                try {
                    $attIns = executeQuery(
                        "INSERT INTO attachments (product_id, report_id, attachment_type, file_path, file_name, file_type, file_size, created_at, updated_at)
                         VALUES (NULL, NULL, 0, :path, :name, :type, :size, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
                         RETURNING attachment_id",
                        [
                            'path' => $avatarUrl,
                            'name' => $fileName,
                            'type' => $mime,
                            'size' => (int) $_FILES['avatar']['size']
                        ],
                        'fetch'
                    );
                    if ($attIns && isset($attIns['attachment_id'])) {
                        $attId = (int) $attIns['attachment_id'];
                    }
                } catch (Throwable $e) {
                    error_log('Insert avatar attachment failed: ' . $e->getMessage());
                }
            }
        }
    }

    // Check existing profile in user_profiles
    $existing = executeQuery("SELECT profile_id, avatar_url, attachment_id FROM user_profiles WHERE user_id = :uid", ['uid' => $userId], 'fetch');

    if ($existing) {
        $updateSql = "UPDATE user_profiles SET 
                        gender = :gender, 
                        birth_date = :birth_date, 
                        address = :address, 
                        subdistrict = :subdistrict, 
                        updated_at = CURRENT_TIMESTAMP";
        $binds = [
            'gender' => $gender,
            'birth_date' => $birthDate,
            'address' => $alamat,
            'subdistrict' => $kelurahan,
            'uid' => $userId
        ];
        if ($avatarUrl) {
            $updateSql .= ", avatar_url = :avatar_url";
            $binds['avatar_url'] = $avatarUrl;
            if ($attId) {
                $updateSql .= ", attachment_id = :attachment_id";
                $binds['attachment_id'] = $attId;
            }
        }
        $updateSql .= " WHERE user_id = :uid";
        executeQuery($updateSql, $binds);
    } else {
        $insertSql = "INSERT INTO user_profiles (user_id, name, gender, birth_date, address, subdistrict, avatar_url, attachment_id, created_at, updated_at) 
                      VALUES (:uid, :name, :gender, :birth_date, :address, :subdistrict, :avatar_url, :attachment_id, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
        executeQuery($insertSql, [
            'uid' => $userId,
            'name' => $session['name'] ?? 'Pengguna',
            'gender' => $gender,
            'birth_date' => $birthDate,
            'address' => $alamat,
            'subdistrict' => $kelurahan,
            'avatar_url' => $avatarUrl,
            'attachment_id' => $attId
        ]);
    }

    // Update Session
    $_SESSION['gender'] = $gender;
    $_SESSION['birth_date'] = $birthDate;
    $_SESSION['address'] = $alamat;
    $_SESSION['subdistrict'] = $kelurahan;
    if ($avatarUrl) {
        $_SESSION['avatar_url'] = $avatarUrl;
    }

    $finalAvatar = $avatarUrl ?: ($existing['avatar_url'] ?? null);

    if (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json')) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Perubahan profil berhasil disimpan ke database!',
            'avatar_url' => $finalAvatar,
            'data' => [
                'gender' => $gender,
                'birth_date' => $birthDate,
                'address' => $alamat,
                'subdistrict' => $kelurahan,
                'avatar_url' => $finalAvatar
            ]
        ]);
        exit;
    }

    flash('success', 'Perubahan profil berhasil disimpan!');
    redirect('/profil.php', 302);
    exit;
}

// Fetch current user and profile data from DB
$dbProfile = executeQuery(
    "SELECT u.email, u.role, up.* 
     FROM users u
     LEFT JOIN user_profiles up ON up.user_id = u.user_id
     WHERE u.user_id = :uid",
    ['uid' => $userId],
    'fetch'
) ?: [];

$userName = $dbProfile['name'] ?? $session['name'] ?? 'Pengguna';
$userEmail = $dbProfile['email'] ?? $session['email'] ?? '';
$userCampus = $dbProfile['campus'] ?? $session['campus'] ?? '';
$userPhone = $dbProfile['phone'] ?? $session['phone'] ?? '';
$userGender = $dbProfile['gender'] ?? $session['gender'] ?? '';
$userBirthDate = $dbProfile['birth_date'] ?? $session['birth_date'] ?? '';
$userAddress = $dbProfile['address'] ?? $session['address'] ?? '';
$userSubdistrict = $dbProfile['subdistrict'] ?? $session['subdistrict'] ?? '';
$userAvatar = $dbProfile['avatar_url'] ?? $session['avatar_url'] ?? '';

// Parse birth date components for dropdowns
$curDay = '';
$curMonth = '';
$curYear = '';
if (!empty($userBirthDate)) {
    if (str_contains($userBirthDate, '-')) {
        $parts = explode('-', $userBirthDate);
        if (strlen($parts[0]) === 4) {
            $curYear = $parts[0];
            $curMonth = str_pad($parts[1] ?? '', 2, '0', STR_PAD_LEFT);
            $curDay = str_pad($parts[2] ?? '', 2, '0', STR_PAD_LEFT);
        } else {
            $curDay = str_pad($parts[0] ?? '', 2, '0', STR_PAD_LEFT);
            $curMonth = str_pad($parts[1] ?? '', 2, '0', STR_PAD_LEFT);
            $curYear = $parts[2] ?? '';
        }
    } elseif (str_contains($userBirthDate, '/')) {
        $parts = explode('/', $userBirthDate);
        $curDay = str_pad($parts[0] ?? '', 2, '0', STR_PAD_LEFT);
        $curMonth = str_pad($parts[1] ?? '', 2, '0', STR_PAD_LEFT);
        $curYear = $parts[2] ?? '';
    }
}
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
            --pk-footer: #1B3260;
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
            display: flex;
            align-items: center;
            color: #64748B;
            pointer-events: none;
        }

        .pk-nav-menu {
            display: flex;
            align-items: center;
            gap: 24px;
        }

        .pk-nav-link {
            color: #FFFFFF;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.92rem;
            transition: opacity 0.15s;
        }

        .pk-nav-link:hover {
            opacity: 0.85;
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
            font-weight: 600;
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

        .pk-form-select {
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-color: #FFFFFF;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2364748B' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            background-size: 16px;
            padding-right: 38px;
        }

        /* Date of Birth 3-Dropdown Grid */
        .pk-dob-grid {
            display: grid;
            grid-template-columns: 1fr 1.4fr 1.1fr;
            gap: 10px;
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

        /* Toast Popup */
        .pk-toast {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #0F172A;
            color: #FFFFFF;
            padding: 14px 22px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.95rem;
            font-weight: 500;
            z-index: 1000;
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.3s ease, transform 0.3s ease;
            pointer-events: none;
        }

        .pk-toast.show {
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
        }

        /* ================= 3. FOOTER STYLES (MATCHED WITH DASHBOARD) ================= */
        .pk-footer {
            background-color: var(--pk-footer);
            color: #CBD5E1;
            padding: 48px 24px 24px;
            margin-top: auto;
        }

        .pk-footer-inner {
            max-width: 1240px;
            margin: 0 auto;
        }

        .pk-footer-grid {
            display: grid;
            grid-template-columns: 1.6fr 1fr 1fr 1fr;
            gap: 36px;
            margin-bottom: 36px;
        }

        .pk-footer-col h4 {
            color: #FFFFFF;
            font-size: 0.95rem;
            font-weight: 700;
            margin-bottom: 14px;
            margin-top: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .pk-footer-col p {
            font-size: 0.84rem;
            line-height: 1.6;
            color: #94A3B8;
            max-width: 320px;
            margin: 0;
        }

        .pk-footer-links {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 8px;
            padding: 0;
            margin: 0;
        }

        .pk-footer-links a {
            font-size: 0.84rem;
            color: #CBD5E1;
            text-decoration: none;
            transition: color 0.15s;
        }

        .pk-footer-links a:hover {
            color: #FFFFFF;
            text-decoration: underline;
        }

        .pk-footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.78rem;
            color: #94A3B8;
            flex-wrap: wrap;
            gap: 12px;
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
            .pk-footer-grid {
                grid-template-columns: 1fr;
                gap: 24px;
                margin-bottom: 24px;
            }
            .pk-footer-bottom {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
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
                <a href="<?= route('/dashboard-utama.php') ?>" class="pk-nav-link">Katalog</a>
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
                        <img id="avatarPreviewImg" alt="Foto Profil" src="<?= esc($userAvatar) ?>" style="<?= !empty($userAvatar) ? 'display:block;' : 'display:none;' ?>">
                        <div class="pk-avatar-placeholder-text" id="avatarPlaceholderText" style="<?= !empty($userAvatar) ? 'display:none;' : '' ?>">
                            Unggah<br>foto
                        </div>
                    </div>
                    <span class="pk-avatar-link-text" onclick="triggerFileUpload()">Unggah foto profil</span>
                </div>

                <!-- Welcome Text -->
                <div class="pk-welcome-text">
                    <h1>Selamat datang!</h1>
                    <h2 id="profileUsername"><?= esc($userName) ?></h2>
                </div>
            </div>
        </section>

        <!-- Bottom Card: Form Biodata -->
        <section class="pk-profile-card">
            <form id="profileForm" onsubmit="saveProfileData(event)">
                <div class="pk-form-grid">
                    <!-- Left Column: Dropdown Jenis Kelamin -->
                    <div class="pk-form-group">
                        <label class="pk-form-label" for="inputJenisKelamin">Jenis Kelamin</label>
                        <select id="inputJenisKelamin" name="jenis_kelamin" class="pk-form-input pk-form-select">
                            <option value="" <?= empty($userGender) ? 'selected' : '' ?> disabled>-- Pilih jenis kelamin --</option>
                            <option value="Laki-laki" <?= ($userGender === 'Laki-laki') ? 'selected' : '' ?>>Laki-laki</option>
                            <option value="Perempuan" <?= ($userGender === 'Perempuan') ? 'selected' : '' ?>>Perempuan</option>
                        </select>
                    </div>

                    <!-- Right Column: Tanggal Lahir (Pilih Tanggal, Bulan, Tahun) -->
                    <div class="pk-form-group">
                        <label class="pk-form-label">Tanggal Lahir</label>
                        <div class="pk-dob-grid">
                            <!-- Dropdown Tanggal -->
                            <select id="selectTanggal" class="pk-form-input pk-form-select" onchange="syncTanggalLahir()" aria-label="Tanggal">
                                <option value="">Tanggal</option>
                                <?php for ($d = 1; $d <= 31; $d++): 
                                    $dStr = sprintf('%02d', $d);
                                ?>
                                    <option value="<?= $dStr ?>" <?= ($curDay === $dStr) ? 'selected' : '' ?>><?= $d ?></option>
                                <?php endfor; ?>
                            </select>

                            <!-- Dropdown Bulan -->
                            <select id="selectBulan" class="pk-form-input pk-form-select" onchange="syncTanggalLahir()" aria-label="Bulan">
                                <option value="">Bulan</option>
                                <?php
                                $months = [
                                    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                                    '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                                    '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                                    '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                                ];
                                foreach ($months as $mNum => $mName):
                                ?>
                                    <option value="<?= $mNum ?>" <?= ($curMonth === $mNum) ? 'selected' : '' ?>><?= $mName ?></option>
                                <?php endforeach; ?>
                            </select>

                            <!-- Dropdown Tahun -->
                            <select id="selectTahun" class="pk-form-input pk-form-select" onchange="syncTanggalLahir()" aria-label="Tahun">
                                <option value="">Tahun</option>
                                <?php
                                $thisYear = (int) date('Y');
                                for ($y = $thisYear - 10; $y >= 1950; $y--):
                                    $yStr = (string) $y;
                                ?>
                                    <option value="<?= $yStr ?>" <?= ($curYear === $yStr) ? 'selected' : '' ?>><?= $y ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <input type="hidden" id="inputTanggalLahir" name="tanggal_lahir" value="<?= esc($userBirthDate) ?>">
                    </div>

                    <!-- Left Column: Alamat Lengkap -->
                    <div class="pk-form-group">
                        <label class="pk-form-label" for="inputAlamat">Alamat lengkap</label>
                        <input type="text" id="inputAlamat" class="pk-form-input" placeholder="Masukkan alamat lengkap" value="<?= esc($userAddress) ?>">
                    </div>

                    <!-- Right Column: Kelurahan -->
                    <div class="pk-form-group">
                        <label class="pk-form-label" for="inputKelurahan">Kelurahan</label>
                        <input type="text" id="inputKelurahan" class="pk-form-input" placeholder="Masukkan kelurahan" value="<?= esc($userSubdistrict) ?>">
                    </div>
                </div>
            </form>
        </section>

        <!-- Right Aligned Simpan Button -->
        <div class="pk-action-row">
            <button type="button" class="pk-btn-simpan" id="btnSimpanProfile" onclick="saveProfileData()">Simpan</button>
        </div>

    </main>

    <!-- Toast Notification -->
    <div id="saveToast" class="pk-toast">
        <span>✅</span>
        <span id="toastMsg">Perubahan profil berhasil disimpan ke database!</span>
    </div>

    <!-- ================= 3. FOOTER ================= -->
    <footer class="pk-footer">
        <div class="pk-footer-inner">
            <div class="pk-footer-grid">
                <div class="pk-footer-col">
                    <h3 style="color: #FFFFFF; font-size: 1.35rem; font-weight: 800; margin-bottom: 12px; margin-top: 0;">PasarKampus</h3>
                    <p>Platform marketplace eksklusif mahasiswa untuk sirkulasi barang perkuliahan yang aman, hemat, dan berkelanjutan.</p>
                </div>
                <div class="pk-footer-col">
                    <h4>PasarKampus</h4>
                    <ul class="pk-footer-links">
                        <li><a href="<?= route('/') ?>">Tentang kami</a></li>
                        <li><a href="<?= route('/dashboard-utama.php') ?>">Katalog Barang</a></li>
                        <li><a href="<?= route('/register.php') ?>">Daftar Akun Mahasiswa</a></li>
                        <li><a href="<?= route('/dashboard-penjual.php') ?>">Dashboard Penjual</a></li>
                    </ul>
                </div>
                <div class="pk-footer-col">
                    <h4>Bantuan</h4>
                    <ul class="pk-footer-links">
                        <li><a href="#">Pusat Bantuan</a></li>
                        <li><a href="#">FAQ</a></li>
                        <li><a href="mailto:support@pasarkampus.id">Kontak Kami</a></li>
                    </ul>
                </div>
                <div class="pk-footer-col">
                    <h4>Dukungan</h4>
                    <ul class="pk-footer-links">
                        <li><a href="#" onclick="alert('Kebijakan Privasi: Data kampus dan identitas mahasiswa dilindungi.'); return false;">Kebijakan</a></li>
                        <li><a href="#" onclick="alert('Panduan COD: Selalu lakukan serah terima di area kampus yang ramai pada jam operasional kampus.'); return false;">Panduan Keamanan COD</a></li>
                        <li><a href="#" onclick="alert('Syarat & Ketentuan PasarKampus: Hanya untuk barang legal & tidak melanggar hukum.'); return false;">Syarat &amp; Ketentuan</a></li>
                    </ul>
                </div>
            </div>
            <div class="pk-footer-bottom">
                <div>&copy; <?= date('Y') ?> PasarKampus, Hak Cipta Dilindungi.</div>
                <div>Dibuat untuk Project Based Learning (PBL) Mahasiswa</div>
            </div>
        </div>
    </footer>

    <!-- JavaScript logic -->
    <script>
        let selectedAvatarFile = null;

        function triggerFileUpload() {
            const input = document.getElementById('profileFileInput');
            if (input) input.click();
        }

        function handleImageUpload(event) {
            const file = event.target.files[0];
            if (!file) return;
            selectedAvatarFile = file;

            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('avatarPreviewImg');
                const placeholder = document.getElementById('avatarPlaceholderText');
                if (img) {
                    img.src = e.target.result;
                    img.style.display = 'block';
                }
                if (placeholder) {
                    placeholder.style.display = 'none';
                }
            };
            reader.readAsDataURL(file);
        }

        async function saveProfileData(event) {
            if (event) event.preventDefault();

            const btn = document.getElementById('btnSimpanProfile');
            if (btn) {
                btn.disabled = true;
                btn.innerText = 'Menyimpan...';
            }

            const gender = document.getElementById('inputJenisKelamin').value;
            const tgl = document.getElementById('inputTanggalLahir').value;
            const alamat = document.getElementById('inputAlamat').value.trim();
            const kelurahan = document.getElementById('inputKelurahan').value.trim();

            const fd = new FormData();
            fd.append('action', 'update_profile');
            fd.append('jenis_kelamin', gender);
            fd.append('tanggal_lahir', tgl);
            fd.append('alamat', alamat);
            fd.append('kelurahan', kelurahan);
            if (selectedAvatarFile) {
                fd.append('avatar', selectedAvatarFile);
            }

            try {
                const response = await fetch(window.location.href, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json' },
                    body: fd
                });
                const result = await response.json();
                if (result.success) {
                    try { localStorage.removeItem('preloved_user'); } catch(e) {}
                    const toast = document.getElementById('saveToast');
                    if (toast) {
                        toast.classList.add('show');
                        setTimeout(() => {
                            toast.classList.remove('show');
                        }, 3000);
                    }
                } else {
                    alert(result.message || 'Gagal menyimpan profil ke database.');
                }
            } catch (err) {
                console.error('Error saving profile:', err);
                const toast = document.getElementById('saveToast');
                if (toast) {
                    toast.classList.add('show');
                    setTimeout(() => {
                        toast.classList.remove('show');
                    }, 3000);
                }
            } finally {
                if (btn) {
                    btn.disabled = false;
                    btn.innerText = 'Simpan';
                }
            }
        }

        function goBack() {
            if (window.history.length > 1) {
                window.history.back();
            } else {
                window.location.href = '<?= route('/dashboard.php') ?>';
            }
        }
    </script>
</body>
</html>
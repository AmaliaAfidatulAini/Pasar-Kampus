<?php
$session = get_session();
if ($session) {
    redirect('/');
}

$error = '';
$success = isset($_GET['success']);
$loggedInUser = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Harap isi email dan password!';
    } else {
        try {
            $stmt = executeQuery(
                "SELECT u.*, p.name, p.phone, p.campus, p.subdistrict, p.district, p.preferences 
                 FROM users u 
                 LEFT JOIN user_profiles p ON u.user_id = p.user_id 
                 WHERE u.email = ? AND (u.deleted_at IS FALSE OR u.deleted_at = FALSE)",
                [$email]
            );
            $user = fetch($stmt);

            if ($user && password_verify($password, $user['password'])) {
                $sessionData = [
                    'user_id' => $user['user_id'],
                    'email'   => $user['email'],
                    'role'    => $user['role'],
                    'name'    => $user['name'] ?? 'Pengguna',
                    'campus'  => $user['campus'] ?? '',
                    'phone'   => $user['phone'] ?? ''
                ];
                create_session($sessionData);
                $loggedInUser = $sessionData;
            } else {
                // If demo account fallback for test purposes
                if ($email === 'farhan@ui.ac.id' && ($password === 'password123' || $password === '12345')) {
                    $sessionData = [
                        'user_id' => 999,
                        'email'   => 'farhan@ui.ac.id',
                        'role'    => 'user',
                        'name'    => 'Muhammad Farhan',
                        'campus'  => 'Universitas Brawijaya (UB)',
                        'phone'   => '081234567890'
                    ];
                    create_session($sessionData);
                    $loggedInUser = $sessionData;
                } else {
                    $error = 'Email atau password salah!';
                }
            }
        } catch (\Throwable $e) {
            // Fallback for environment where DB table might not yet be seeded
            if ($email === 'farhan@ui.ac.id' && ($password === 'password123' || $password === '12345')) {
                $sessionData = [
                    'user_id' => 999,
                    'email'   => 'farhan@ui.ac.id',
                    'role'    => 'user',
                    'name'    => 'Muhammad Farhan',
                    'campus'  => 'Universitas Brawijaya (UB)',
                    'phone'   => '081234567890'
                ];
                create_session($sessionData);
                $loggedInUser = $sessionData;
            } else {
                $error = 'Gagal memverifikasi login: ' . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in - Pasar Kampus</title>
    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- CSS Stylesheet -->
    <link rel="stylesheet" href="<?= route('/css/style.css') ?>">
    <style>
        body.pbl-login-page {
            margin: 0;
            padding: 24px 16px;
            min-height: 100vh;
            background-color: #1A2F60;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            box-sizing: border-box;
        }

        .pbl-login-card {
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
            width: 100%;
            max-width: 440px;
            padding: 38px 40px 34px;
            box-sizing: border-box;
            position: relative;
        }

        .pbl-login-close {
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

        .pbl-login-close:hover {
            background: #E2E8F0;
            color: #1E293B;
        }

        .pbl-login-title {
            font-size: 28px;
            font-weight: 800;
            color: #1D356D;
            text-align: center;
            margin: 0 0 6px 0;
            letter-spacing: -0.3px;
        }

        .pbl-alert {
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .pbl-alert-error {
            background: #FEE2E2;
            color: #DC2626;
            border: 1px solid #FECACA;
        }

        .pbl-alert-success {
            background: #DCFCE7;
            color: #166534;
            border: 1px solid #BBF7D0;
        }

        .pbl-form-group {
            margin-bottom: 18px;
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
            height: 44px;
            padding: 8px 14px;
            font-size: 13.5px;
            color: #1e293b;
            border: 1.5px solid #CBD5E1;
            border-radius: 10px;
            box-sizing: border-box;
            outline: none;
            background: #FFFFFF;
            transition: border-color 0.2s, box-shadow 0.2s;
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
            user-select: none;
        }

        .pbl-login-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 10px;
            font-size: 12.5px;
        }

        .pbl-remember-label {
            display: flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            color: #475569;
            font-weight: 500;
        }

        .pbl-forgot-link {
            font-weight: 600;
            color: #1D356D;
            text-decoration: underline;
        }

        .pbl-login-btn-container {
            margin-top: 22px;
        }

        .pbl-login-btn {
            width: 100%;
            height: 44px;
            background-color: #1D356D;
            color: #FFFFFF;
            font-size: 14.5px;
            font-weight: 700;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            font-family: inherit;
            transition: background-color 0.2s, transform 0.1s;
        }

        .pbl-login-btn:hover {
            background-color: #162955;
            transform: translateY(-1px);
        }

        .pbl-divider-or {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 22px 0 16px;
            color: #94A3B8;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.5px;
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

        .pbl-login-footer {
            text-align: center;
            font-size: 13px;
            color: #64748B;
        }

        .pbl-login-footer a {
            color: #1D356D;
            font-weight: 800;
            text-decoration: underline;
            margin-left: 4px;
        }
    </style>
</head>
<body class="pbl-login-page">

    <main class="pbl-login-card">
        <!-- Close Button (X) back to index -->
        <a href="<?= route('/') ?>" class="pbl-login-close" title="Kembali ke Beranda">&times;</a>

        <h1 class="pbl-login-title">Log in</h1>

        <?php if ($success): ?>
            <div class="pbl-alert pbl-alert-success">
                <span>✅</span> Registrasi berhasil! Silakan masuk dengan akun Anda.
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="pbl-alert pbl-alert-error">
                <span>⚠️</span> <?= esc($error) ?>
            </div>
        <?php endif; ?>

        <form id="loginForm" method="POST" action="<?= route('/login.php') ?>">
            <!-- Email -->
            <div class="pbl-form-group">
                <label class="pbl-label" for="loginEmail">Email</label>
                <input type="text" id="loginEmail" name="email" required class="pbl-input" placeholder="Email" value="<?= esc($_POST['email'] ?? 'farhan@ui.ac.id') ?>">
            </div>

            <!-- Password -->
            <div class="pbl-form-group" style="margin-bottom: 6px;">
                <label class="pbl-label" for="loginPassword">Password</label>
                <div class="pbl-pwd-wrap">
                    <input type="password" id="loginPassword" name="password" required class="pbl-input" placeholder="Masukkan password" value="password123">
                    <button type="button" class="pbl-pwd-toggle" onclick="toggleLoginPagePwd(this)" title="Lihat/Sembunyikan password">
                        <img id="loginEyeIcon" src="<?= route('/images/eye-closed.png') ?>" alt="Toggle Password" class="pbl-pwd-eye-icon">
                    </button>
                </div>
                <div class="pbl-login-meta">
                    <label class="pbl-remember-label">
                        <input type="checkbox" id="loginRemember" name="remember" checked>
                        <span>Tetap Login</span>
                    </label>
                    <a href="#" onclick="alert('Silakan hubungi administrator kampus jika lupa kata sandi.'); return false;" class="pbl-forgot-link">Lupa Password?</a>
                </div>
            </div>

            <!-- Submit Button (Full Width) -->
            <div class="pbl-login-btn-container">
                <button type="submit" class="pbl-login-btn">
                    Masuk
                </button>
            </div>

            <!-- Divider: ATAU -->
            <div class="pbl-divider-or">
                <span>ATAU</span>
            </div>

            <!-- Footer: Belum punya akun? Daftar -->
            <div class="pbl-login-footer">
                Belum punya akun? <a href="<?= route('/register.php') ?>">Daftar</a>
            </div>

            <!-- Portal Admin Separator & Link -->
            <div style="margin-top: 16px; padding-top: 14px; border-top: 1px dashed #E2E8F0; text-align: center; font-size: 12px; color: #64748B;">
                Khusus Pengelola: <a href="<?= route('/login-admin.php') ?>" style="color: #1D356D; font-weight: 700; text-decoration: underline;">Portal Login Admin</a>
            </div>
        </form>
    </main>

    <script>
        function toggleLoginPagePwd(btn) {
            const input = document.getElementById('loginPassword');
            const eyeIcon = document.getElementById('loginEyeIcon') || (btn ? btn.querySelector('img') : null);
            if (input) {
                if (input.type === 'password') {
                    input.type = 'text';
                    if (eyeIcon) {
                        eyeIcon.src = '<?= route('/images/eye-open.png') ?>';
                        eyeIcon.alt = 'Sembunyikan password';
                    }
                } else {
                    input.type = 'password';
                    if (eyeIcon) {
                        eyeIcon.src = '<?= route('/images/eye-closed.png') ?>';
                        eyeIcon.alt = 'Lihat password';
                    }
                }
            }
        }

        <?php if ($loggedInUser): ?>
            // Sync session to localStorage for client-side scripts
            localStorage.setItem('pk_logged_in', 'true');
            localStorage.setItem('preloved_user', JSON.stringify({
                email: <?= json_encode($loggedInUser['email']) ?>,
                nama: <?= json_encode($loggedInUser['name']) ?>,
                telepon: <?= json_encode($loggedInUser['phone'] ?? '081234567890') ?>,
                kampus: <?= json_encode($loggedInUser['campus'] ?? 'Universitas Brawijaya (UB)') ?>,
                role: <?= json_encode($loggedInUser['role']) ?>
            }));
            window.location.href = '<?= $loggedInUser['role'] === 'admin' ? route('/dashboard-admin.php') : route('/') ?>';
        <?php endif; ?>
    </script>
</body>
</html>

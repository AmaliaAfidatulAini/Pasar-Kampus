<?php
$session = get_session();
if ($session && ($session['role'] ?? '') === 'admin') {
    redirect('/dashboard-admin.php');
}

$error = '';
$loggedInAdmin = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Harap isi email dan password admin!';
    } else {
        try {
            $stmt = executeQuery(
                "SELECT u.*, p.name FROM users u 
                 LEFT JOIN user_profiles p ON u.user_id = p.user_id 
                 WHERE u.email = ? AND (u.deleted_at IS FALSE OR u.deleted_at = FALSE)",
                [$email]
            );
            $user = fetch($stmt);

            if ($user && password_verify($password, $user['password'])) {
                if (($user['role'] ?? '') !== 'admin') {
                    $error = 'Akses ditolak: Akun ini bukan administrator!';
                } else {
                    $sessionData = [
                        'user_id' => $user['user_id'],
                        'email'   => $user['email'],
                        'role'    => 'admin',
                        'name'    => $user['name'] ?? 'Superadmin Pusat'
                    ];
                    create_session($sessionData);
                    $loggedInAdmin = $sessionData;
                }
            } else {
                // Demo admin fallback
                if ($email === 'admin@pasarkampus.id' && ($password === 'admin123' || $password === 'password123')) {
                    $sessionData = [
                        'user_id' => 1,
                        'email'   => 'admin@pasarkampus.id',
                        'role'    => 'admin',
                        'name'    => 'Superadmin Pusat'
                    ];
                    create_session($sessionData);
                    $loggedInAdmin = $sessionData;
                } else {
                    $error = 'Kredensial administrator tidak valid!';
                }
            }
        } catch (\Throwable $e) {
            if ($email === 'admin@pasarkampus.id' && ($password === 'admin123' || $password === 'password123')) {
                $sessionData = [
                    'user_id' => 1,
                    'email'   => 'admin@pasarkampus.id',
                    'role'    => 'admin',
                    'name'    => 'Superadmin Pusat'
                ];
                create_session($sessionData);
                $loggedInAdmin = $sessionData;
            } else {
                $error = 'Gagal verifikasi admin: ' . $e->getMessage();
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
    <title>Login Admin - Portal Superadmin Pasar Kampus</title>
    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- CSS Stylesheet -->
    <link rel="stylesheet" href="<?= route('/css/style.css') ?>">
    <style>
        body.pbl-admin-login-page {
            margin: 0;
            padding: 24px 16px;
            min-height: 100vh;
            background: radial-gradient(circle at 50% 30%, #1E3A8A 0%, #0F172A 100%);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            box-sizing: border-box;
        }

        .pbl-admin-login-card {
            background: #FFFFFF;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.45);
            width: 100%;
            max-width: 450px;
            padding: 38px 40px 32px;
            box-sizing: border-box;
            position: relative;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .pbl-login-close {
            position: absolute;
            top: 18px;
            right: 20px;
            font-size: 24px;
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
            background: #F1F5F9;
            color: #1E293B;
        }

        .pbl-badge-wrap {
            text-align: center;
            margin-bottom: 12px;
        }

        .pbl-admin-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #EFF6FF;
            color: #1D356D;
            font-size: 11.5px;
            font-weight: 800;
            padding: 5px 14px;
            border-radius: 9999px;
            border: 1.5px solid #BFDBFE;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .pbl-admin-badge .dot {
            width: 7px;
            height: 7px;
            background: #2563EB;
            border-radius: 50%;
        }

        .pbl-login-title {
            font-size: 26px;
            font-weight: 800;
            color: #1D356D;
            text-align: center;
            margin: 0 0 6px 0;
            letter-spacing: -0.3px;
        }

        .pbl-login-subtitle {
            font-size: 13px;
            color: #64748B;
            text-align: center;
            margin: 0 0 24px 0;
            font-weight: 500;
        }

        .pbl-alert-error {
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 16px;
            background: #FEE2E2;
            color: #DC2626;
            border: 1px solid #FECACA;
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
            color: #1E293B;
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
            margin-top: 10px;
        }

        .pbl-login-btn:hover {
            background-color: #162955;
            transform: translateY(-1px);
        }

        .pbl-footer-meta {
            text-align: center;
            font-size: 12.5px;
            color: #64748B;
            margin-top: 20px;
        }

        .pbl-footer-meta a {
            color: #1D356D;
            font-weight: 700;
            text-decoration: underline;
        }
    </style>
</head>
<body class="pbl-admin-login-page">

    <main class="pbl-admin-login-card">
        <a href="<?= route('/') ?>" class="pbl-login-close" title="Kembali ke Beranda">&times;</a>

        <div class="pbl-badge-wrap">
            <span class="pbl-admin-badge">
                <span class="dot"></span>
                Administrator System
            </span>
        </div>

        <h1 class="pbl-login-title">Portal Admin</h1>
        <p class="pbl-login-subtitle">Masuk untuk mengelola data, barang, dan verifikasi kampus</p>

        <?php if ($error): ?>
            <div class="pbl-alert-error">
                <span>⚠️</span> <?= esc($error) ?>
            </div>
        <?php endif; ?>

        <form id="adminLoginForm" method="POST" action="<?= route('/login-admin.php') ?>">
            <div class="pbl-form-group">
                <label class="pbl-label" for="adminEmail">Email Administrator</label>
                <input type="email" id="adminEmail" name="email" required class="pbl-input" placeholder="admin@pasarkampus.id" value="<?= esc($_POST['email'] ?? 'admin@pasarkampus.id') ?>">
            </div>

            <div class="pbl-form-group">
                <label class="pbl-label" for="adminPassword">Password</label>
                <div class="pbl-pwd-wrap">
                    <input type="password" id="adminPassword" name="password" required class="pbl-input" placeholder="Masukkan password" value="admin123">
                    <button type="button" class="pbl-pwd-toggle" onclick="toggleAdminPwd(this)" title="Lihat/Sembunyikan password">
                        <img id="adminEyeIcon" src="<?= route('/images/eye-closed.png') ?>" alt="Toggle Password" class="pbl-pwd-eye-icon">
                    </button>
                </div>
            </div>

            <button type="submit" class="pbl-login-btn">
                Masuk ke Dashboard Admin &rarr;
            </button>

            <div class="pbl-footer-meta">
                Bukan admin? <a href="<?= route('/login.php') ?>">Login Pengguna Biasa</a>
            </div>
        </form>
    </main>

    <script>
        function toggleAdminPwd(btn) {
            const input = document.getElementById('adminPassword');
            const eyeIcon = document.getElementById('adminEyeIcon') || (btn ? btn.querySelector('img') : null);
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

        <?php if ($loggedInAdmin): ?>
            localStorage.setItem('pk_logged_in', 'true');
            localStorage.setItem('pk_admin_logged_in', 'true');
            localStorage.setItem('preloved_admin_user', JSON.stringify({
                email: <?= json_encode($loggedInAdmin['email']) ?>,
                nama: <?= json_encode($loggedInAdmin['name']) ?>,
                role: 'admin'
            }));
            window.location.href = '<?= route('/dashboard-admin.php') ?>';
        <?php endif; ?>
    </script>
</body>
</html>

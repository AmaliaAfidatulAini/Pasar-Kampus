<?php
$session = get_session();
$redirectTarget = redirect_target($_GET['redirect'] ?? '');

if (isset($session['user_id'])) {
    if (($session['role'] ?? '') === 'admin') {
        redirect('/admin', 302);
    }
    redirect($redirectTarget !== '' ? $redirectTarget : '/', 302);
}

$email = esc($_POST['email'] ?? '');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PasarKampus - Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/lib/bootstrap.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/main.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/flasher.css') ?>">
</head>

<body class="bg-primary d-flex align-items-center justify-content-center min-vh-100 p-3">
    <?php Component::include('common/flasher'); ?>

    <main class="card border-0 rounded-4 shadow-lg w-100" style="max-width: 440px">
        <div class="card-body p-4 p-sm-5">
            <header class="text-center mb-4">
                <h1 class="h3 fw-bold text-primary mb-1">Masuk</h1>
            </header>

            <form id="loginForm" method="POST" action="<?= route('/login/post') ?>">
                <input type="hidden" name="redirect" value="<?= esc($redirectTarget) ?>">
                <fieldset class="border-0 p-0 m-0">
                    <legend class="visually-hidden">Login details</legend>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small" for="email">Email</label>
                        <input class="form-control" type="email" id="email" name="email" placeholder="email@example.com" value="<?= $email ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small" for="password">Password</label>
                        <div class="position-relative" data-password-wrap>
                            <input class="form-control pe-5" type="password" id="password" name="password" minlength="8" placeholder="Password" required>
                            <button type="button" id="password-toggle" class="btn btn-link position-absolute top-50 end-0 translate-middle-y p-2 text-secondary" aria-label="Lihat password">
                                <img src="<?= asset('img/eye-closed.jpg') ?>" class="pk-icon" alt="">
                            </button>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mt-2 small" data-auth-meta>
                            <label class="form-check d-flex align-items-center gap-2 mb-0 ps-0" for="loginRemember">
                                <input class="form-check-input m-0" type="checkbox" id="loginRemember" checked>
                                <span class="form-check-label text-secondary">Tetap Login</span>
                            </label>
                            <a class="fw-semibold" href="#" onclick="alert('Fitur reset password (Demo)'); return false;">Lupa Password?</a>
                        </div>
                    </div>
                </fieldset>

                <div class="mt-4">
                    <button class="btn btn-primary w-100" type="submit">Masuk</button>
                </div>

                <footer class="text-center small text-secondary mt-3">
                    Belum punya akun? <a class="fw-bold" href="<?= route('register') ?>">Daftar</a>
                </footer>
            </form>
        </div>
    </main>

    <script src="<?= asset('js/auth.js') ?>"></script>
</body>
</html>

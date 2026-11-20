<?php
$session = get_session();
if (isset($session['user_id'])) {
    redirect(($session['role'] ?? '') === 'admin' ? '/admin' : '/', 302);
}

$email = esc($_POST['email'] ?? '');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PasarKampus - Register</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/lib/bootstrap.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/main.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/flasher.css') ?>">
</head>

<body class="bg-primary d-flex flex-column align-items-center justify-content-center min-vh-100 p-3">
    <?php Component::include('common/flasher'); ?>

    <main class="card border-0 rounded-4 shadow-lg w-100" style="max-width: 440px">
        <div class="card-body p-4 p-sm-5">
            <header class="text-center mb-4">
                <h1 class="h3 fw-bold text-primary mb-1">Daftar</h1>
            </header>

            <form id="regAuthForm" method="POST" action="<?= route('/register/post') ?>">
                <fieldset class="border-0 p-0 m-0">
                    <legend class="visually-hidden">Registration details</legend>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small" for="regEmail">Email</label>
                        <input class="form-control" type="email" id="regEmail" name="email" placeholder="email@example.com" value="<?= $email ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small" for="password">Password</label>
                        <div class="position-relative" data-password-wrap>
                            <input class="form-control pe-5" type="password" id="password" name="password" minlength="8" placeholder="Password" required>
                            <button type="button" id="password-toggle" class="btn btn-link position-absolute top-50 end-0 translate-middle-y p-2 text-secondary" aria-label="Lihat password">
                                <img src="<?= asset('img/eye-closed.jpg') ?>" class="pk-icon" alt="">
                            </button>
                        </div>
                    </div>
                </fieldset>

                <div class="mt-4">
                    <button class="btn btn-primary w-100" type="submit">Daftar</button>
                </div>

                <footer class="text-center small text-secondary mt-3">
                    Sudah punya akun? <a class="fw-bold" href="<?= route('login') ?>">Masuk</a>
                </footer>
            </form>
        </div>
    </main>

    <script src="<?= asset('js/auth.js') ?>"></script>
</body>
</html>

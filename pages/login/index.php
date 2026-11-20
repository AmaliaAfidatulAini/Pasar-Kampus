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
    <title>PasarKampus - Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset("css/auth.css") ?>">
    <link rel="stylesheet" href="<?= asset("css/flasher.css") ?>">
</head>

<body data-auth-page="login">
    <?php Component::include('common/flasher'); ?>

    <main>
        <header>
            <h1>Masuk</h1>
        </header>

        <form id="loginForm" method="POST" action="<?= route('/login/post') ?>">
            <fieldset>
                <legend>Login details</legend>
                <div>
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="email@example.com" value="<?= $email ?>" required>
                </div>

                <div>
                    <label for="password">Password</label>
                    <div data-password-wrap>
                        <input type="password" id="password" name="password" minlength="8" placeholder="Password" required>
                        <button type="button" id="password-toggle">
                            <img src="<?= asset("img/eye-closed.jpg") ?>" alt="Toggle Password">
                        </button>
                    </div>
                    <div data-auth-meta>
                        <label>
                            <input type="checkbox" id="loginRemember" checked>
                            <span>Tetap Login</span>
                        </label>
                        <a href="#" onclick="alert('Fitur reset password (Demo)'); return false;">Lupa Password?</a>
                    </div>
                </div>
            </fieldset>

            <div data-submit>
                <button type="submit">Masuk</button>
            </div>

            <footer>
                Belum punya akun? <a href="<?= route("register") ?>">Daftar</a>
            </footer>
        </form>
    </main>

    <script src="<?= asset("js/auth.js") ?>"></script>
</body>
</html>

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
    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- CSS Stylesheet -->
    <link rel="stylesheet" href="<?= asset("css/auth.css") ?>">
    <link rel="stylesheet" href="<?= asset("css/flasher.css") ?>">
</head>

<body data-auth-page="register">
    <?php Component::include('common/flasher'); ?>

    <main>
        <header>
            <h1>Daftar</h1>
        </header>

        <form id="regAuthForm" method="POST" action="<?= route('/register/post') ?>">
            <fieldset>
                <legend>Registration details</legend>
                <div>
                    <label for="regEmail">Email</label>
                    <input type="email" id="regEmail" name="email" placeholder="email@example.com" value="<?= $email ?>" required>
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

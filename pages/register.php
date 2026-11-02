<?php

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
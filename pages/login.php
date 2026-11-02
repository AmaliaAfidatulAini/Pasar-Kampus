<?php 

use Azolee\Validator\Validator;

$email = $_POST['email'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $validation = Validator::make([
        'email' => 'bail|required|string|email|max:50',
        'password' => 'bail|required|string',
    ], $_POST);

    if ($validation->isFailed()) {
        flash('error', implode(' ', parse_errors($validation)));
    } else {
        $result = (object) $validation->validated();

        $user = executeQuery(
            "SELECT u.*, p.name FROM users u 
            LEFT JOIN user_profiles p ON u.user_id = p.user_id 
            WHERE u.email = :email AND u.deleted_at = FALSE", 
            ["email" => $result->email],
            "fetch"
        );

        if ($user && password_verify($result->password, $user['password'])) {
            create_session($user);
            redirect("/", 302);
        }

        flash('error', 'Email atau Password Salah!');
    }
}
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
    <?php include_component('flasher'); ?>

    <main>
        <header>
            <h1>Masuk</h1>
        </header>

        <form id="loginForm" method="POST" action="<?= route('login') ?>">
            <fieldset>
                <legend>Login details</legend>
                <div>
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="email@example.com" value="<?= esc($email) ?>" required>
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
<?php
session_exclude();

$error = null;
$registered = ($_GET['registered'] ?? null) === '1';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $email = is_string($email) ? trim($email) : '';
    $password = is_string($password) ? $password : '';

    if ($email === '' || $password === '') {
        $error = 'Email and password are required.';
    } else {
        $account = executeQuery(
            'SELECT id, name, email, password, role FROM users WHERE email = :email',
            ['email' => $email],
            'fetch'
        );

        if (!is_array($account) || !password_verify($password, $account['password'])) {
            $error = 'Invalid email or password.';
        } else {
            create_session([
                'id' => $account['id'],
                'user' => $account['name'],
                'email' => $account['email'],
                'role' => $account['role'],
            ]);
            redirect($account['role'] === 'admin' ? '/dashboard' : '/barang', 303);
        }
    }
}

include_component('header', ['title' => 'Login']);
?>

<h1>Login</h1>
<?php if ($registered): ?><p>Registration successful. You can now log in.</p><?php endif; ?>
<?php if ($error !== null): ?><p><?= esc($error) ?></p><?php endif; ?>
<form method="post" action="<?= esc(route('/login')) ?>">
    <label for="email">Email</label>
    <input id="email" name="email" type="email" value="<?= esc($email ?? '') ?>" required>
    <br \>
    <label for="password">Password</label>
    <input id="password" name="password" type="password" required>
    
    <br \>
    <button type="submit">Log in</button>
</form>
<p><a href="<?= esc(route('/register')) ?>">Create an account</a></p>

<?php include_component('footer'); ?>

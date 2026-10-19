<?php
session_exclude();

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $passwordConfirmation = $_POST['password_confirmation'] ?? '';
    $name = is_string($name) ? trim($name) : '';
    $email = is_string($email) ? trim($email) : '';
    $password = is_string($password) ? $password : '';
    $passwordConfirmation = is_string($passwordConfirmation) ? $passwordConfirmation : '';

    if ($name === '' || $email === '' || $password === '') {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } elseif ($password !== $passwordConfirmation) {
        $error = 'Passwords do not match.';
    } else {
        try {
            executeQuery(
                'INSERT INTO users (name, email, password) VALUES (:name, :email, :password)',
                [
                    'name' => $name,
                    'email' => $email,
                    'password' => password_hash($password, PASSWORD_DEFAULT),
                ]
            );
            redirect('/login?registered=1', 303);
        } catch (PDOException $exception) {
            $error = $exception->getCode() === '23505'
                ? 'That email address is already registered.'
                : 'Registration failed. Please try again.';
        }
    }
}

include_component('header', ['title' => 'Register']);
?>

<h1>Register</h1>
<?php if ($error !== null): ?><p><?= esc($error) ?></p><?php endif; ?>
<form method="post" action="<?= esc(route('/register')) ?>">
    <label for="name">Name</label>
    <input id="name" name="name" value="<?= esc($name ?? '') ?>" required>

    <br \>
    <label for="email">Email</label>
    <input id="email" name="email" type="email" value="<?= esc($email ?? '') ?>" required>

    <br \>
    <label for="password">Password</label>
    <input id="password" name="password" type="password" required>

    <br \>
    <label for="password_confirmation">Confirm password</label>
    <input id="password_confirmation" name="password_confirmation" type="password" required>

    <br \>
    <button type="submit">Register</button>
</form>

<?php include_component('footer'); ?>

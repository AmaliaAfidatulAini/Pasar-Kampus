<?php 

session_start();
require_once __DIR__ . '/../core/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';


$stmt = executeQuery("SELECT u.*, p.name FROM users u LEFT JOIN user_profiles p ON u.user_id = p.user_id WHERE u.email = ? AND u.deleted_at = FALSE", [$email]);
$user = fetch($stmt);

if ($user && password_verify($password, $user['password'])) {

$_SESSION['user_id'] = $user['user_id'];
$_SESSION['email'] = $user['email'];
$_SESSION['role'] = $user['role']; 
$_SESSION['name'] = $user['name'] ?? 'Pengguna';

header("Location: index.php");
    exit;
        } else {
        $error = "Email atau password salah!";
        }
    }
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Login - Pasar Kampus</title>
</head>

<body>
    <h2>Login Pasar Kampus</h2>
    <?php if (isset($_GET['success'])): ?><p style="color: green;">Registrasi berhasil, silakan login!</p><?php endif; ?>
    <?php if ($error): ?><p style="color: red;"><?= $error ?></p><?php endif; ?>

    <form action="login.php" method="POST">
        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit">Login</button>
    </form>
    <p>Belum punya akun? <a href="register.php">Daftar disini</a></p>
</body>

</html>
